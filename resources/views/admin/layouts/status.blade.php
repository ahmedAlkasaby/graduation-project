<script type="text/javascript">
    $(document).ready(function() {

        // One entry per module whose list view renders a status toggle. The key
        // is the table name, which is what tables.status uses for the class.
        var statusRoutes = {
            'cities': "{{ route('admin.cities.status') }}",
        };

        $.each(statusRoutes, function(table, url) {
            $('body').on('change', '.' + table + '-status', function() {
                var obj = $(this);
                var status = obj.is(':checked') ? 1 : 0;

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        _token: $('meta[name="_token"]').attr('content'),
                        id: obj.attr('data-id'),
                        status: status
                    },
                    cache: false,
                    dataType: 'json',
                    success: function(data) {
                        // The controller answers false when it refuses the
                        // change, so only then does the switch snap back.
                        if (data !== "" && data == true) {
                            obj.prop('checked', status);
                        } else {
                            obj.prop('checked', !status);
                        }
                    },
                    error: function() {
                        obj.prop('checked', !status);
                    }
                });

                return false;
            });
        });

    });
</script>
