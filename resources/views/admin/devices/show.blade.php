@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title')
    {{ __('Device Details') }}
@stop
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-1">{{ __('Device Details') }}</h5>
                <p class="text-muted small mb-0">{{ $device->device_name ?? __('Unnamed Device') }}</p>
            </div>
            <div>
                @if($device->isPending())
                    <a href="{{ route('admin.devices.pending') }}" class="btn btn-sm btn-outline-secondary">{{ __('Back to Pending') }}</a>
                @else
                    <a href="{{ route('admin.devices.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('Back to Devices') }}</a>
                @endif
            </div>
        </div>

        <div class="card-body">
            @include('admin.errors.alerts')
            <div class="alert alert-danger d-none" id="device-error"></div>
            <div class="alert alert-success d-none" id="device-success"></div>

            <div class="row">
                <!-- Basic Information -->
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2 mb-3">{{ __('Basic Information') }}</h6>
                    <table class="table table-sm table-borderless">
                        <tbody>
                            <tr>
                                <th style="width: 180px;">{{ __('Employee') }}</th>
                                <td>
                                    {{ $device->user?->full_name }}
                                    @if($device->user?->employee)
                                        <br><small class="text-muted">{{ $device->user->employee->job_title ?? '' }}</small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('Device Name') }}</th>
                                <td>{{ $device->device_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Platform') }}</th>
                                <td><span class="badge bg-info">{{ $device->getPlatformLabel() }}</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('State') }}</th>
                                <td><span class="badge bg-{{ $device->state?->color() ?? 'secondary' }} device-state">{{ $device->state?->label() }}</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('Installation ID') }}</th>
                                <td><code class="small">{{ $device->installation_id }}</code></td>
                            </tr>
                            @if($device->imei)
                            <tr>
                                <th>{{ __('IMEI') }}</th>
                                <td><code class="small">{{ $device->imei }}</code></td>
                            </tr>
                            @endif
                            <tr>
                                <th>{{ __('Created At') }}</th>
                                <td>{{ $device->created_at?->format('Y-m-d H:i:s') }} ({{ $device->created_at?->diffForHumans() }})</td>
                            </tr>
                            @if($device->expires_at)
                            <tr>
                                <th>{{ __('Expires At') }}</th>
                                <td class="{{ $device->isExpired() ? 'text-danger' : '' }}">
                                    {{ $device->expires_at?->format('Y-m-d H:i:s') }}
                                    @if($device->isExpired())
                                        <span class="badge bg-danger">{{ __('Expired') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Security & Review Information -->
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2 mb-3">{{ __('Security & Review') }}</h6>
                    <table class="table table-sm table-borderless">
                        <tbody>
                            @if($device->credential_id)
                            <tr>
                                <th style="width: 180px;">{{ __('Credential ID') }}</th>
                                <td><code class="small">{{ Str::limit($device->credential_id, 30) }}</code></td>
                            </tr>
                            @endif
                            @if($device->credential_type)
                            <tr>
                                <th>{{ __('Credential Type') }}</th>
                                <td>{{ str_replace('_', ' ', ucwords($device->credential_type, '_')) }}</td>
                            </tr>
                            @endif
                            @if($device->public_key)
                            <tr>
                                <th>{{ __('Public Key') }}</th>
                                <td><code class="small text-break">{{ Str::limit($device->public_key, 60) }}</code></td>
                            </tr>
                            @endif
                            <tr>
                                <th>{{ __('Reviewed By') }}</th>
                                <td>{{ $device->reviewer?->full_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Reviewed At') }}</th>
                                <td>{{ $device->reviewed_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
                            </tr>
                            @if($device->rejectionReason)
                            <tr>
                                <th>{{ __('Rejection Reason') }}</th>
                                <td class="text-danger">{{ $device->rejectionReason->nameLang() }}</td>
                            </tr>
                            @endif
                            @if($device->note)
                            <tr>
                                <th>{{ __('Internal Note') }}</th>
                                <td>{{ $device->note }}</td>
                            </tr>
                            @endif
                            @if($device->last_seen_at)
                            <tr>
                                <th>{{ __('Last Seen') }}</th>
                                <td>{{ $device->last_seen_at?->format('Y-m-d H:i:s') }} ({{ $device->last_seen_at?->diffForHumans() }})</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Metadata -->
            @if($device->attestation_metadata || $device->device_metadata)
            <div class="row mt-4">
                <div class="col-12">
                    <h6 class="border-bottom pb-2 mb-3">{{ __('Technical Details') }}</h6>
                </div>

                @if($device->attestation_metadata)
                <div class="col-md-6">
                    <h6 class="small text-muted">{{ __('Attestation Metadata') }}</h6>
                    <pre class="bg-light p-3 rounded"><code>{{ json_encode($device->attestation_metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
                @endif

                @if($device->device_metadata)
                <div class="col-md-6">
                    <h6 class="small text-muted">{{ __('Device Metadata') }}</h6>
                    <pre class="bg-light p-3 rounded"><code>{{ json_encode($device->device_metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
                @endif
            </div>
            @endif

            <!-- Actions -->
            @if($device->isPending() && !$device->isExpired())
            <div class="row mt-4">
                <div class="col-12">
                    <div class="border-top pt-3">
                        <button type="button" class="btn btn-success" id="btn-approve" data-id="{{ $device->id }}">
                            <i class="fas fa-check"></i> {{ __('Approve Device') }}
                        </button>
                        <button type="button" class="btn btn-danger" id="btn-reject" data-id="{{ $device->id }}">
                            <i class="fas fa-times"></i> {{ __('Reject Device') }}
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Modals (same as pending.blade.php) -->
    @if($device->isPending() && !$device->isExpired())
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
                        <select class="form-select" id="reject-reason" required>
                            <option value="">{{ __('Choose') }}</option>
                            @foreach($rejectionReasons as $rejectionReasonId => $rejectionReasonName)
                                <option value="{{ $rejectionReasonId }}">{{ $rejectionReasonName }}</option>
                            @endforeach
                        </select>
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

    @section('after_foot')
    <script>
        $(function () {
            var errorBox = $('#device-error');
            var successBox = $('#device-success');
            var deviceId = {{ $device->id }};

            // Approve button
            $('#btn-approve').click(function () {
                $('#approve-note').val('');
                $('#approveModal').modal('show');
            });

            // Reject button
            $('#btn-reject').click(function () {
                $('#reject-reason').val('');
                $('#reject-note').val('');
                $('#rejectModal').modal('show');
            });

            // Confirm approve
            $('#confirm-approve').click(function () {
                var btn = $(this);
                var note = $('#approve-note').val();

                btn.prop('disabled', true);
                errorBox.addClass('d-none').text('');
                successBox.addClass('d-none').text('');

                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.devices.approve', $device->id) }}',
                    data: {
                        _token: $('meta[name="_token"]').attr('content'),
                        note: note
                    },
                    dataType: 'json',
                    success: function (data) {
                        if (data && data.success) {
                            $('#approveModal').modal('hide');
                            successBox.removeClass('d-none').text(data.message);
                            // Update state badge
                            $('.device-state').attr('class', 'badge bg-' + data.state_color + ' device-state').text(data.state_label);
                            // Hide action buttons
                            $('#btn-approve, #btn-reject').closest('.border-top').remove();
                            // Redirect after 2 seconds
                            setTimeout(function() {
                                window.location.href = '{{ route('admin.devices.pending') }}';
                            }, 2000);
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
                var btn = $(this);
                var reason = $('#reject-reason').val();
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
                    url: '{{ route('admin.devices.reject', $device->id) }}',
                    data: {
                        _token: $('meta[name="_token"]').attr('content'),
                        rejection_reason_id: reason,
                        note: note
                    },
                    dataType: 'json',
                    success: function (data) {
                        if (data && data.success) {
                            $('#rejectModal').modal('hide');
                            successBox.removeClass('d-none').text(data.message);
                            // Update state badge
                            $('.device-state').attr('class', 'badge bg-' + data.state_color + ' device-state').text(data.state_label);
                            // Hide action buttons
                            $('#btn-approve, #btn-reject').closest('.border-top').remove();
                            // Redirect after 2 seconds
                            setTimeout(function() {
                                window.location.href = '{{ route('admin.devices.pending') }}';
                            }, 2000);
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
    @endsection
    @endif
@stop
