@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title')
    {{ __('Holiday Requests') }}
@stop
@section('content')
    @include('admin.holiday_requests.components.head-search')

    @include('admin.holiday_requests.components.status-filter')

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6 head-label">
                    <h5 class="card-title">{{ __('Holiday Requests') }}</h5>
                </div>
                <div class="col-md-6 dt-action-buttons text-end pt-3 pt-md-0">
                    @include('admin.layouts.components.page-head', [
                        'table' => 'holiday_requests',
                        'create_label' => __('Create HolidayRequest'),
                    ])
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            @include('admin.errors.alerts')
            @include('admin.layouts.table-head')
            @include('admin.holiday_requests.components.table')
            @include('admin.layouts.table-foot')
            @include('admin.layouts.paginate-all')
        </div>
    </div>
@stop
@section('after_foot')
    @include('admin.layouts.filter')
    @include('admin.layouts.status')
    @include('admin.layouts.delete')

    <script>
        $(document).ready(function() {
            // Approve button click handler
            $('.approve-btn').on('click', function() {
                const button = $(this);
                const url = button.data('url');
                const row = button.closest('tr');

                Swal.fire({
                    title: '{{ __("Are you sure?") }}',
                    text: '{{ __("Do you want to approve this holiday request?") }}',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{ __("Yes, approve it!") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Disable button and show loading
                        button.prop('disabled', true);
                        button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ __("Processing...") }}');

                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    // Update status badge
                                    const statusCell = row.find('td:eq(6)');
                                    statusCell.html('<span class="badge bg-label-success">' + response.data.status_label + '</span>');

                                    // Update reviewed by
                                    const reviewedByCell = row.find('td:eq(7)');
                                    reviewedByCell.html(response.data.reviewed_by || '-');

                                    // Update actions cell
                                    const actionsCell = row.find('td:eq(8)');
                                    actionsCell.html('<span class="badge bg-label-secondary">{{ __("No Actions") }}</span>');

                                    // Show success message
                                    Swal.fire({
                                        icon: 'success',
                                        title: '{{ __("Success!") }}',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: '{{ __("Error!") }}',
                                        text: response.message
                                    });
                                    button.prop('disabled', false);
                                    button.html('<i class="ti ti-check"></i> {{ __("Approve") }}');
                                }
                            },
                            error: function(xhr) {
                                const message = xhr.responseJSON?.message || '{{ __("An error occurred") }}';
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __("Error!") }}',
                                    text: message
                                });
                                button.prop('disabled', false);
                                button.html('<i class="ti ti-check"></i> {{ __("Approve") }}');
                            }
                        });
                    }
                });
            });

            // Reject form submit handler
            $('.reject-form').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const url = form.data('url');
                const modal = form.closest('.modal');
                const row = modal.closest('tr').length ? modal.closest('tr') : $('tr').has(modal);
                const submitBtn = form.find('button[type="submit"]');
                const reasonSelect = form.find('select[name="rejection_reason_id"]');
                const reasonId = reasonSelect.val();

                // Clear previous errors
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').text('');

                if (!reasonId) {
                    reasonSelect.addClass('is-invalid');
                    reasonSelect.siblings('.invalid-feedback').text('{{ __("Please select a rejection reason") }}');
                    return;
                }

                // Disable button and show loading
                submitBtn.prop('disabled', true);
                submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ __("Processing...") }}');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        rejection_reason_id: reasonId
                    },
                    success: function(response) {
                        if (response.success) {
                            // Close modal
                            const bsModal = bootstrap.Modal.getInstance(modal[0]);
                            bsModal.hide();

                            // Find the correct row using the modal's ID
                            const modalId = modal.attr('id');
                            const requestId = modalId.replace('rejectModal', '');
                            const targetRow = $('button[data-bs-target="#' + modalId + '"]').closest('tr');

                            // Update status badge
                            const statusCell = targetRow.find('td:eq(6)');
                            statusCell.html('<span class="badge bg-label-danger">' + response.data.status_label + '</span>');

                            // Update reviewed by
                            const reviewedByCell = targetRow.find('td:eq(7)');
                            reviewedByCell.html(response.data.reviewed_by || '-');

                            // Update actions cell
                            const actionsCell = targetRow.find('td:eq(8)');
                            actionsCell.html('<span class="badge bg-label-secondary">{{ __("No Actions") }}</span>');

                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("Success!") }}',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __("Error!") }}',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON?.errors;
                            if (errors && errors.rejection_reason_id) {
                                reasonSelect.addClass('is-invalid');
                                reasonSelect.siblings('.invalid-feedback').text(errors.rejection_reason_id[0]);
                            }
                        } else {
                            const message = xhr.responseJSON?.message || '{{ __("An error occurred") }}';
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __("Error!") }}',
                                text: message
                            });
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false);
                        submitBtn.html('{{ __("Reject Request") }}');
                    }
                });
            });
        });
    </script>
@stop
