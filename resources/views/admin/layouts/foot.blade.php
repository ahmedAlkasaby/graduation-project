    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    {{-- forms-pickers.js: login has no .flatpickr-* element (and flatpickr.js isn't loaded here) --}}
    {{-- <script src="{{ asset('assets/js/forms-pickers.js') }}"></script> --}}

    <script src="{{ asset('js/admin/parsley.min.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/swiper/swiper.js') }}"></script>
    {{-- datatables-bs5 is v1.10.8: it exposes $.fn.DataTable only, never the global DataTable
         that app.js uses. Pages needing tables include admin.layouts.foots.corejs (v2.2.1). --}}
    {{-- <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script> --}}

    <!-- Main JS -->
    {{-- <script src="{{ asset('assets/js/main.js') }}"></script> --}}
    {{-- app.js: login has no #datatables-basic table --}}
    {{-- <script src="{{ asset('assets/js/app.js') }}"></script> --}}
    <!-- Page JS -->

    @yield('jsfiles')
