        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- CSRF Token -->
        <meta name="_token" content="{{ csrf_token() }}"/>
        <title> @yield('title')
            {{--  &#8211; {{ config('app.name', 'Project') }}  --}}
        </title>
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="{{ asset('css/admin/bootstrap.min.css') }}" >
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset('css/admin/font-awesome.min.css') }}" >
        <!-- Ionicons -->
        <link rel="stylesheet" href="{{ asset('css/admin/ionicons.min.css') }}" >
        <link rel="stylesheet" href="{{ asset('css/admin/datatables.bootstrap.min.css') }}" >
        <!-- Theme style -->
        {{--  <link rel="stylesheet" href="{{ asset('css/admin/app.min.css') }}">  --}}
        {{--  <link rel="stylesheet" href="{{ asset('css/admin/all-skins.min.css') }}">  --}}
        <link rel="stylesheet" href="{{ asset('css/admin/print/generator.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/print/template.css') }}">
        @if($admin_language == "ar")
        <link rel="stylesheet" href="{{ asset('css/admin/ar/bootstrap.min.css') }}" >
        {{--  <link rel="stylesheet" href="{{ asset('css/admin/ar/app.min.css') }}"> --}}
        {{--  <link rel="stylesheet" href="{{ asset('css/admin/ar/all-skins.min.css') }}">  --}}
        @endif
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

