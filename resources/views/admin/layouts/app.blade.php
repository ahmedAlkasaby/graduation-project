<!DOCTYPE html>

<html lang="{{ $admin_language }}"
    data-theme="theme-default"
    dir="{{ $admin_dir }}"
    data-template="vertical-menu-template-no-customizer"
    class="{{$admin_theme_style}}-style layout-navbar-fixed layout-menu-fixed layout-compact">

<head>
    @include('admin.layouts.meta')
    @if(isset($head))
    @include('admin.layouts.heads.'.$head)
    @else
    @include('admin.layouts.heads.head')
    @endif
    @yield('after_head')
</head>

<body data-language="{{ $admin_language ?? 'en' }}">
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar" data-url="{{ route('admin.index') }}" data-language="{{ $admin_language ?? 'en' }}" data-user="{{ $user_id ?? 0 }}">
        <div class="layout-container">

            {{--include('admin.layouts.header')--}}
            @include('admin.layouts.sidebar')
            <div class="layout-page">
                @include('admin.layouts.navbar')
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <div class="container-fluid flex-grow-1 container-p-y">
                        @yield('head_content')
                        @yield('content')
                    </div>
                    @include('admin.layouts.footer')
                </div>
                <!-- /Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
        <!-- / layout-container -->
        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    @if(isset($foot))
    @include('admin.layouts.foots.'.$foot)
    @else
    @include('admin.layouts.foots.foot')
    @endif
    <script>
        const change_Theme = "{{ route('admin.theme.change') }}";
        const change_Language = "{{ route('admin.lang.change') }}";
    </script>
    @yield('after_foot')
    @include('admin.layouts.pusher')
</body>

</html>
