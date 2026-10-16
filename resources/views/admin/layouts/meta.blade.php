<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
{{-- <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no"> --}}

<!-- CSRF Token -->
<meta name="_token" content="{{ csrf_token() }}" />
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>
    @if (isset($title))
    {{ $title }}
    @else
    @yield('title')
    @endif
    &#8211;
    {{ __(config('app.name')) }}
</title>
