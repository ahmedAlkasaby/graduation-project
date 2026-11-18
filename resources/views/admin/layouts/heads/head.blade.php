@php
// $admin_theme_style = "dark";
@endphp
<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
<!-- BEGIN: Theme CSS-->
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">


<link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />

<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables/buttons.dataTables.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables/dataTables.dataTables.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables/colReorder.bootstrap5.min.css') }}" />

<!-- Core CSS -->
@if ( $admin_theme_style =='dark' )
<link rel="stylesheet" href="{{ asset('assets/vendor/css/'.$admin_dir.'/core-dark.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/css/'.$admin_dir.'/theme-default-dark.css') }}" />
@elseif($admin_theme_style =='semi-dark')
<link rel="stylesheet" href="{{ asset('assets/vendor/css/'.$admin_dir.'/core.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/css/'.$admin_dir.'/theme-semi-dark.css') }}" />
@else
<link rel="stylesheet" href="{{ asset('assets/vendor/css/'.$admin_dir.'/core.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/css/'.$admin_dir.'/theme-default.css') }}" />
@endif
<!-- Vendors CSS -->
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}" />

<link rel="stylesheet" href="{{asset('assets/vendor/libs/toastr/toastr.css')}}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/css/'.$admin_dir.'/main.css') }}" />
<link rel="stylesheet" href="{{ asset('css/admin/lightgallery-bundle.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/systemira.css') }}" />

@yield('styles')
<!-- Helpers -->
<script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
<!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
<!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
{{--  <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>  --}}
<!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
<!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
<script src="{{ asset('assets/js/config.js') }}"></script>

