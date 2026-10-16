
@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.{{ $repeater_class }}').repeater({
                show: function() {
                    /* the clone arrives holding the row it was copied from */
                    $(this).find('input[type="text"], textarea').val('');
                    $(this).fadeIn();
                },
                hide: function(deleteElement) {
                    $(this).fadeOut(deleteElement);
                }
            });
        });
    </script>
@endpush
