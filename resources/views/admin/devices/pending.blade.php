@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title')
    {{ __('Pending Device Registrations') }}
@stop
@section('content')
    @include('admin.devices.components.head-search-pending')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6 head-label">
                    <h5 class="card-title">{{ __('Pending Device Registrations') }}</h5>
                    <p class="text-muted small mb-0">{{ __('Review and approve device registration requests from employees') }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('admin.devices.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-list"></i> {{ __('All Devices') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            @include('admin.errors.alerts')
            <div class="alert alert-danger d-none mx-3" id="device-error"></div>
            <div class="alert alert-success d-none mx-3" id="device-success"></div>
            @include('admin.layouts.table-head')
            @include('admin.devices.components.table-pending')
            @include('admin.layouts.table-foot')
            @include('admin.layouts.paginate-all')
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Reject Device Registration') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Rejection Reason') }} <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reject-reason" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Internal Note') }}</label>
                        <textarea class="form-control" id="reject-note" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirm-reject">{{ __('Reject') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Approve Device Registration') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        {{ __('This will allow the user to authenticate from this device.') }}
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Internal Note') }}</label>
                        <textarea class="form-control" id="approve-note" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-success" id="confirm-approve">{{ __('Approve') }}</button>
                </div>
            </div>
        </div>
    </div>
@stop
@section('after_foot')
    @include('admin.layouts.filter')
    <script>
        $(function () {
            var errorBox = $('#device-error');
            var successBox = $('#device-success');
            var pendingDeviceId = null;

            // Approve button click
            $('body').on('click', '.btn-approve-device', function () {
                pendingDeviceId = $(this).data('id');
                $('#approve-note').val('');
                $('#approveModal').modal('show');
            });

            // Reject button click
            $('body').on('click', '.btn-reject-device', function () {
                pendingDeviceId = $(this).data('id');
                $('#reject-reason').val('');
                $('#reject-note').val('');
                $('#rejectModal').modal('show');
            });

            // Confirm approve
            $('#confirm-approve').click(function () {
                if (!pendingDeviceId) return;

                var btn = $(this);
                var note = $('#approve-note').val();
                
                btn.prop('disabled', true);
                errorBox.addClass('d-none').text('');
                successBox.addClass('d-none').text('');

                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.devices.approve', ':id') }}'.replace(':id', pendingDeviceId),
                    data: {
                        _token: $('meta[name="_token"]').attr('content'),
                        note: note
                    },
                    dataType: 'json',
                    success: function (data) {
                        if (data && data.success) {
                            $('#approveModal').modal('hide');
                            successBox.removeClass('d-none').text(data.message);
                            // Remove the row from the table
                            $('#device-row-' + pendingDeviceId).fadeOut(300, function() {
                                $(this).remove();
                            });
                            // Scroll to success message
                            $('html, body').animate({ scrollTop: successBox.offset().top - 100 }, 300);
                        } else {
                            errorBox.removeClass('d-none').text((data && data.message) ? data.message : '{{ __('Error') }}');
                        }
                        btn.prop('disabled', false);
                    },
                    error: function (xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '{{ __('Error') }}';
                        errorBox.removeClass('d-none').text(msg);
                        btn.prop('disabled', false);
                    }
                });
            });

            // Confirm reject
            $('#confirm-reject').click(function () {
                if (!pendingDeviceId) return;

                var btn = $(this);
                var reason = $('#reject-reason').val().trim();
                var note = $('#reject-note').val();

                if (!reason) {
                    alert('{{ __('Please provide a rejection reason') }}');
                    return;
                }

                btn.prop('disabled', true);
                errorBox.addClass('d-none').text('');
                successBox.addClass('d-none').text('');

                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.devices.reject', ':id') }}'.replace(':id', pendingDeviceId),
                    data: {
                        _token: $('meta[name="_token"]').attr('content'),
                        reason: reason,
                        note: note
                    },
                    dataType: 'json',
                    success: function (data) {
                        if (data && data.success) {
                            $('#rejectModal').modal('hide');
                            successBox.removeClass('d-none').text(data.message);
                            // Remove the row from the table
                            $('#device-row-' + pendingDeviceId).fadeOut(300, function() {
                                $(this).remove();
                            });
                            // Scroll to success message
                            $('html, body').animate({ scrollTop: successBox.offset().top - 100 }, 300);
                        } else {
                            errorBox.removeClass('d-none').text((data && data.message) ? data.message : '{{ __('Error') }}');
                        }
                        btn.prop('disabled', false);
                    },
                    error: function (xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '{{ __('Error') }}';
                        errorBox.removeClass('d-none').text(msg);
                        btn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@stop
