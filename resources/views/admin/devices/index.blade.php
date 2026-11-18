@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title')
    {{ __('Devices') }}
@stop
@section('content')
    @include('admin.devices.components.head-search')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6 head-label">
                    <h5 class="card-title">{{ __('Devices') }}</h5>
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            @include('admin.errors.alerts')
            <div class="alert alert-danger d-none mx-3" id="device-error"></div>
            @include('admin.layouts.table-head')
            @include('admin.devices.components.table')
            @include('admin.layouts.table-foot')
            @include('admin.layouts.paginate-all')
        </div>
    </div>
@stop
@section('after_foot')
    @include('admin.layouts.filter')
    <script>
        $(function () {
            var errorBox = $('#device-error');

            $('body').on('click', '.device-action', function () {
                var btn = $(this);
                var id = btn.data('id');
                var url = btn.data('url');
                var actions = btn.closest('.device-actions');

                actions.find('.device-action').prop('disabled', true);
                errorBox.addClass('d-none').text('');

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: { _token: $('meta[name="_token"]').attr('content') },
                    dataType: 'json',
                    success: function (data) {
                        if (data && data.success) {
                            var row = $('#device-row-' + id);
                            var badge = row.find('.device-state');
                            // Recolor the badge to match the new state.
                            badge.attr('class', 'badge device-state bg-' + (data.state_color || 'secondary'))
                                .text(data.state_label);
                            // The request is resolved: replace the buttons with the final state label.
                            actions.html('<span class="text-muted">' + data.state_label + '</span>');
                        } else {
                            errorBox.removeClass('d-none').text((data && data.message) ? data.message : '{{ __('Error') }}');
                            actions.find('.device-action').prop('disabled', false);
                        }
                    },
                    error: function (xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '{{ __('Error') }}';
                        errorBox.removeClass('d-none').text(msg);
                        actions.find('.device-action').prop('disabled', false);
                    }
                });
            });
        });
    </script>
@stop
