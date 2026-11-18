@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title')
    {{ __('Employee Invitations') }}
@stop
@section('content')
    @include('admin.user_invitations.components.head-search')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6 head-label">
                    <h5 class="card-title">{{ __('Employee Invitations') }}</h5>
                </div>
                <div class="col-md-6 dt-action-buttons text-end pt-3 pt-md-0">
                    @include('admin.layouts.components.page-head', [
                        'table' => 'user_invitations',
                        'create_label' => __('Invite Employee'),
                    ])
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            @include('admin.errors.alerts')
            @include('admin.layouts.table-head')
            @include('admin.user_invitations.components.table')
            @include('admin.layouts.table-foot')
            @include('admin.layouts.paginate-all')
        </div>
    </div>
@stop
@section('after_foot')
    @include('admin.layouts.filter')
    <script>
        $(function () {
            var csrf = $('meta[name="_token"]').attr('content');

            function postAction(url, done) {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: { _token: csrf },
                    dataType: 'json',
                    success: function (data) {
                        if (data && data.success) {
                            toastr && toastr.success(data.message);
                            done(data);
                        } else {
                            toastr && toastr.error((data && data.message) ? data.message : '{{ __('Error') }}');
                        }
                    },
                    error: function (xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '{{ __('Error') }}';
                        toastr && toastr.error(msg);
                    }
                });
            }

            // Resend — reissues the invitation email. No row change needed.
            $('body').on('click', '.invitation-resend', function (e) {
                e.preventDefault();
                var link = $(this);
                if (link.hasClass('disabled')) {
                    return;
                }
                link.addClass('disabled');
                postAction(link.data('url'), function () {})
                    .always(function () { link.removeClass('disabled'); });
            });

            // Revoke — cancels the outstanding invitation; reflect it in the row.
            $('body').on('click', '.invitation-revoke', function (e) {
                e.preventDefault();
                var link = $(this);
                if (! window.confirm('{{ __('Confirm Delete ?') }}')) {
                    return;
                }
                postAction(link.data('url'), function () {
                    var row = $('#invitation-row-' + link.data('id'));
                    row.find('.invitation-status-cell')
                        .html('<span class="badge bg-secondary">{{ __('Revoked') }}</span>');
                    // Revoked invitations can no longer be resent or revoked again.
                    row.find('.invitation-actions-cell').html('<span class="text-muted">-</span>');
                });
            });
        });
    </script>
@stop
