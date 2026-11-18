<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        tinymce.init({
            selector: "textarea#my-textarea",
            theme: "modern",
            height: 300,
            plugins: [
                "advlist autolink link image lists charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
                "table contextmenu directionality emoticons paste textcolor responsivefilemanager code"
            ],
            toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
            toolbar2: "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code ",
            image_advtab: true,
            external_filemanager_path: "/filemanager/",
            filemanager_title: "Responsive Filemanager",
            filemanager_access_key: "admin_panel",
            external_plugins: {
                "filemanager": "/filemanager/plugin.min.js"
            }

        });
    });
</script>
