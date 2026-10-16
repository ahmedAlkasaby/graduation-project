@extends('admin.layouts.app')
@section('title') {{ __('About') }}
@stop
@section('content')
    @include('admin.errors.alerts')
    {!! html()->form('POST', route('admin.settings.about.store'))->attribute('data-parsley-validate', '')->attribute('enctype', 'multipart/form-data')->open() !!}
    <div class="row">
        <div class="col-sm-12">
            <div class="box">
                <div class="box-body">
                    @include('admin.layouts.forms.files.image', [
                        'width' => '25',
                        'image_name' => 'about_image',
                    ])
                    @include('admin.layouts.forms.products.content-en')
                    @include('admin.layouts.forms.products.content-ar')
                    @include('admin.layouts.forms.buttons.save')
                </div>
            </div>
        </div>
    </div>
    @include('admin.layouts.forms.close')
@stop
@section('after_foot')
    {{-- @include('admin.layouts.image') --}}

@stop
