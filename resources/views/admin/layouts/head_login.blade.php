@php
// Login is always dark. This has to be repeated here even though login.blade.php already
// sets it: the admin.* view composer runs on this partial too and overwrites any value
// passed down by @include, so an inherited $admin_theme_style would be back to 'light'.
// Keep this in sync with login.blade.php or the stylesheets below stop matching the
// {{ $admin_theme_style }}-style class on <html>.
$admin_theme_style = 'dark';
@endphp
<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
<!-- BEGIN: Theme CSS-->
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

<!-- Icons -->
<link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />

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


<link rel="stylesheet" href="{{ asset('assets/css/systemira.css') }}" />

<!-- Vendors CSS -->
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}" />

<link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css') }}" />

<link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />


<!-- Helpers -->
<script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
<!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
<!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
<script src="{{ asset('assets/js/config.js') }}"></script>
