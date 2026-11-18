<script type="text/javascript">

    $('body').on('click', '.remove_image_link', function () {
        $(this).prev().prev().prev().val('');
        $(this).prev().prev().attr('src', '').hide();
    });

    $('.iframe-btn').fancybox({
        'type': 'iframe',
        maxWidth: 900,
        maxHeight: 600,
        fitToView: true,
        width: '100%',
        height: '100%',
        autoSize: false,
        closeClick: true,
        closeBtn: true
    });

    function responsive_filemanager_callback(field_id) {
//        alert(field_id);
        $('#' + field_id).next().attr('src', document.getElementById(field_id).value).show();
        parent.$.fancybox.close();

    }

    $(document).ready(function () {
        $('.city-repeater').repeater({
            defaultValues: {
                'price': 0
            },
            show: function () {
                $(this).find(".select2-container--default").remove();
                $(this).find(".select2").select2({
                    dir: "rtl"
                });
                $(this).fadeIn();
            },
            hide: function (deleteElement) {
                $(this).fadeOut(deleteElement);
            }
        });


    });

</script>
