<script type="text/javascript">
    $(document).ready(function() {

        // One entry per module whose list view renders a status toggle. The key
        // is the table name, which is what tables.status uses for the class.
        // ":id" is replaced with the row's id: PATCH admin/<table>/{id}/status.
        var statusRoutes = {
            'users': "{{ route('admin.users.status', ':id') }}",
            'cities': "{{ route('admin.cities.status', ':id') }}",
            'regions': "{{ route('admin.regions.status', ':id') }}",
            'case_categories': "{{ route('admin.case_categories.status', ':id') }}",
            'case_assistances': "{{ route('admin.case_assistances.status', ':id') }}",
        };

        $.each(statusRoutes, function(table, url) {
            $('body').on('change', '.' + table + '-status', function() {
                var obj = $(this);
                var wanted = obj.is(':checked');

                $.ajax({
                    type: 'POST',
                    url: url.replace(':id', obj.attr('data-id')),
                    data: {
                        _token: $('meta[name="_token"]').attr('content'),
                        _method: 'PATCH'
                    },
                    cache: false,
                    dataType: 'json',
                    success: function(data) {
                        // The switch always shows what the server saved.
                        obj.prop('checked', !!data.active);
                    },
                    error: function(xhr) {
                        obj.prop('checked', !wanted);
                        var message = xhr.responseJSON && xhr.responseJSON.message;
                        if (message && typeof toastr !== 'undefined') {
                            toastr.error(message);
                        }
                    }
                });

                return false;
            });
        });

    });
</script>
