
@extends('admin.layouts.foots.foot')
@section('jsFiles')
    <!-- Vendors JS -->
    <!-- Forms js -->
    <script src="{{ asset('assets/vendor/libs/bs-stepper/bs-stepper.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <!-- date picker js -->
    <script src="{{ asset('assets/vendor/libs/date/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/date/bootstrap-datetimepicker.min.js') }}"></script>
    <!-- End date picker js -->
    {{--  <script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>  --}}
    <!-- End Forms js -->
    @yield('editorFiles')
    <!-- End Forms js -->
<!-- Page JS -->
    {{--  <script src="{{ asset('assets/js/form-wizard-numbered.js') }}"></script>
    <script src="{{ asset('assets/js/form-wizard-validation.js') }}"></script>  --}}

    <script src="{{ asset('assets/vendor/libs/dropzone/dropzone.js') }}"></script>
    <script>
        // translations and the server's file ceiling, read by dropzone-upload.js
        window.dropzoneStrings = {
            processing: @json(__('Processing image...')),
            tooMany: @json(__('Too many images in one save. Please upload at most :count at a time.', ['count' => (int) (ini_get('max_file_uploads') ?: 20) - 1])),
        };
        window.dropzoneMaxFiles = {{ (int) (ini_get('max_file_uploads') ?: 20) }};
    </script>
    <script src="{{ asset('assets/js/dropzone-upload.js') }}"></script>
    <script src="{{ asset('assets/js/tinymce/tinymce.min.js') }}"></script>
    {{--  <script src="{{ asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js') }}"></script>  --}}
    <script src="{{ asset('assets/js/jquery.repeater.min.js') }}"></script>
    <script src="{{ asset('js/admin/'.$admin_language.'/'.$admin_language.'.js') }}"></script>
    <script src="{{ asset('js/admin/'.$admin_language.'/extra.js') }}"></script>
    <script src="{{ asset('js/admin/'.$admin_language.'/app.js') }}"></script>
    <!-- Main JS -->
    @stop
    @section('mainFiles')
    {{-- <script src="{{ asset('assets/js/app.js') }}"></script> --}}
    @stop
