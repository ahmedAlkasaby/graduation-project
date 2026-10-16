@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Setting') }}
@stop
@section('content')
    @include('admin.errors.alerts')
    <div class="content-wrapper">
        <div class="container-fluid flex-grow-1 container-p-y">
            <div class="row">
                {!! html()->form('POST', route('admin.settings.store'))->attribute('data-parsley-validate', '')->attribute('enctype', 'multipart/form-data')->open() !!}

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
                        </div>
                        <div class="bs-stepper-content">
                            <!-- site Details -->
                            <div id="site-details" class="content">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        @include('admin.layouts.forms.fields.text', [
                                            'text_name' => 'site_title',
                                            'text_value' => $setting['site_title'] ?? null,
                                            'label_name' => __('Site Title'),
                                            'label_req' => true,
                                        ])
                                    </div>
                                    <div class="col-md-6">
                                        @include('admin.layouts.forms.fields.text', [
                                            'text_name' => 'site_phone',
                                            'text_value' => $setting['site_phone'] ?? null,
                                            'label_name' => __('Site Phone'),
                                            'label_req' => true,
                                        ])
                                    </div>
                                    <div class="col-md-6">
                                        @include('admin.layouts.forms.fields.text', [
                                            'text_name' => 'site_email',
                                            'text_value' => $setting['site_email'] ?? null,
                                            'label_name' => __('Site Email'),
                                            'label_req' => true,
                                            'text_type' => 'email',
                                        ])
                                    </div>
                                    {{-- <div class="col-md-6">
                                    @include('admin.layouts.forms.fields.text', [
                                    'text_name' => 'print_color',
                                    'text_value' => $setting['print_color'] ?? null,
                                    'label_name' => __('Print Color'),
                                    'label_req' => true,
                                    'text_type' => 'email'
                                    ])
                                </div> --}}


                                    {{-- <div class="col-md-6 ">
                                    <label class="form-label" for="site_open">{{ __('Site Country') }}</label>
                                    {!! Form::select('site_country',$countries, $setting['site_country'] ?? null, [
                                    'class' => 'select2 form-select',
                                    'required' => '',
                                    'id' =>'site_country'
                                    ]) !!}
                                </div> --}}
                                    <div class="col-md-12 ">
                                        <label class="form-label">{{ __('Site Logo') }}</label>
                                        @include('admin.layouts.forms.dropzone.dropzone', [
                                            'name' => 'image',
                                            'existingImageUrl' => !empty($image) ? asset($image) : null,
                                            'image_size' => $imagesize ?? null,
                                        ])
                                    </div>
                                </div>
                            </div>

                            @include('admin.settings.social')

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
    <!-- /Default Wizard -->
@stop
