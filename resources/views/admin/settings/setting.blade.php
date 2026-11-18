@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Setting') }}
@stop
@section('content')
    @include('admin.errors.alerts')
    <div class="content-wrapper">
        <div class="container-fluid flex-grow-1 container-p-y">
            <div class="row">
                {!! html()->form('POST', $formAction ?? route('admin.settings.developer.setting.store'))->attribute('data-parsley-validate', '')->attribute('enctype', 'multipart/form-data')->open() !!}

                <!-- Default Wizard -->
                <div class="col-12 mb-4">
                    <small class="text-light fw-medium"></small>
                    <div class="bs-stepper wizard-numbered mt-2">
                        <div class="bs-stepper-header">
                            <div class="step" data-target="#site-details">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle"><i class=" ti ti-world"></i></span>
                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">{{ __('Site Details') }}</span>
                                        <span class="bs-stepper-subtitle">{{ __('Add Site details ') }} </span>
                                    </span>
                                </button>
                            </div>
                            <div class="line">
                                <i class="ti ti-chevron-right"></i>
                            </div>
                            <div class="step" data-target="#social-links">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle "><i class="ti ti-link"></i></span>
                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">{{ __('Social Links') }} </span>
                                        <span class="bs-stepper-subtitle">{{ __('Add Social Links') }}</span>
                                    </span>
                                </button>
                            </div>
                            <div class="line">
                                <i class="ti ti-chevron-right"></i>
                            </div>
                            <div class="step" data-target="#hr-settings">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle"><i class="ti ti-building-factory-2"></i></span>
                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">{{ __('HR Settings') }}</span>
                                        <span class="bs-stepper-subtitle">{{ __('Work schedule & attendance rules') }}</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                        <div class="bs-stepper-content">
                            <!-- site Details -->
                            @include('admin.settings.components.details')


                            @include('admin.settings.components.social')

                            @include('admin.settings.components.hr')

                            <div class="content-header mb-3"></div>
                            <div class="col-12 d-flex justify-content-between">
                                <div class="col-12">
                                    @include('admin.layouts.forms.buttons.form-save')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @include('admin.layouts.forms.close')
            </div>
        </div>
    </div>
@stop
@section('mainFiles')
    @include('admin.layouts.forms.dropzone.dropzoneJs')
@stop
