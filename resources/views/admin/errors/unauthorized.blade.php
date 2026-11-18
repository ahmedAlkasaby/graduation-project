@extends('admin.layouts.app')
@section('title')  {{ __('Unauthorized Exeption') }}
@stop
{{-- @section('head_content')
<h1> {{ __('Unauthorized Page') }}</h1>
@stop --}}
@section('content')
{{-- <div class="error-page">
    <div class="error-content">
        <h2 class="text-red">{{ __('No Way!') }}</h2>
        <h3><i class="fa fa-warning text-red"></i> {{ __('Oops! You are not authorized to do this.') }}</h3>
        <p>
            {{ __('Meanwhile, you may') }} <a href="/{{ $admin_url }}">{{ __('return to home') }}</a>.
        </p>
    </div>
    <!-- /.error-content -->
</div>
<!-- /.error-page --> --}}
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="text-center">
            <h2 class="mb-1 mx-2">{{__('You are not authorized!')}}</h2>
            <p class="mb-4 mx-2">
                <a href={{route('admin.index') }} class="btn btn-primary mb-4" > {{('Back to home')}}</a><br>
                {{__('You do not have permission to view this page using the credentials that you have provided while login. 
                Please contact your site administrator.')}}
            </p>
            <div class="mt-4">
                <img
                    src="{{ asset('assets/img/illustrations/page-misc-you-are-not-authorized.png') }}"
                    alt="page-misc-not-authorized"
                    width="170"
                    class="img-fluid" />
            </div>
        </div>
    </div>
@stop