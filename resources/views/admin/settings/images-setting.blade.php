@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])

@section('title') {{ __('Image Setting') }} @stop

@section('content')
    @include('admin.errors.alerts')

    <div class="content-wrapper">
        <div class="container-fluid flex-grow-1 container-p-y">
            <div class="row">

                {!! html()->form('POST', route('admin.images.setting.store'))->acceptsFiles()->attribute('data-parsley-validate', '')->open() !!}

                <div class="col-12 mb-4">
                    <div class="bs-stepper wizard-numbered mt-2">

                        <div class="bs-stepper-header">
                            <div class="step active">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle">
                                        <i class="ti ti-photo"></i>
                                    </span>
                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">{{ __('Add Image Setting') }}</span>
                                        <span class="bs-stepper-subtitle">{{ __('Key & Image') }}</span>
                                    </span>
                                </button>
                            </div>
                        </div>

                        <div class="bs-stepper-content">
                            <div class="content active">
                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('Key Name') }}</label>
                                        {!! html()->select('key', imageType(), null)->class('select2 form-select')->required()->placeholder(__('Select Key')) !!}
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('Image') }}</label>
                                        <input type="file" name="image" class="form-control" required>
                                    </div>

                                </div>
                            </div>
                        </div>



                        <div class="content-header mb-3"></div>
                        <div class="col-12">
                            @include('admin.layouts.forms.buttons.form-save')
                        </div>

                    </div>
                </div>
            </div>

            @include('admin.layouts.forms.close')

        </div>
    </div>
    </div>
@stop
