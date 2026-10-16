@extends('admin.layouts.app')
@section('title') {{ __('Contact Us') }}
@stop
@section('content')
    @include('admin.errors.alerts')

    {!! html()->form('POST', route('admin.settings.contact.store'))->attribute('data-parsley-validate', '')->attribute('enctype', 'multipart/form-data')->open() !!}

    <div class="row">
        <div class="col-sm-12">
            <div class="box">
                <div class="box-body">
                    <div class="form-group">
                        <label>{{ __('Email') }}</label>
                        {!! html()->text('site_email', $site_email ?? null)->class('form-control')->required()->attribute('data-parsley-type', 'email') !!}
                    </div>
                    <div class="form-group">
                        <label>{{ __('Phone') }}</label>
                        {!! html()->text('site_phone', $site_phone ?? null)->class('form-control') !!}
                    </div>
                    <div class="form-group">
                        <label>{{ __('Address') }}</label>
                        {!! html()->text('address', $address ?? null)->class('form-control') !!}
                    </div>
                    @include('admin.layouts.forms.buttons.save')
                </div>
            </div>
        </div>

    </div>

    @include('admin.layouts.forms.close')
@stop
