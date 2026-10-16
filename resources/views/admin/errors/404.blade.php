@extends('admin.layouts.app')
@section('title') {{ __('404') }}
@stop
@section('head_content')
 <h1>{{ __('404 Error Page') }}</h1>
@stop
@section('content')
  
      <div class="error-page">
        <h2 class="headline text-yellow"> {{ __('404') }}</h2>
        <div class="error-content">
          <h3><i class="fa fa-warning text-yellow"></i>{{ __('Oops! Page not found.') }}</h3>
          <p>
            {{ __('We could not find the page you were looking for.') }} {{ __('Meanwhile, you may') }}
             <a href="/{{ $admin_url }}">{{ __('return to home') }}</a>{{ __('or try using the search form.') }} 
          </p>
        </div>
        <!-- /.error-content -->
@stop