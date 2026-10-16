@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Setting') }}
@stop
@section('content')
    @include('admin.errors.alerts')
    <div class="content-wrapper">
        <div class="container-fluid flex-grow-1 container-p-y">
            <div class="row">
                {!! html()->form('POST', route('admin.developer.setting.store'))->attribute('data-parsley-validate', '')->open() !!}

                <!-- Default Wizard -->
                <div class="col-12 mb-4">
                    <small class="text-light fw-medium"></small>
                    <div class="bs-stepper wizard-numbered mt-2">
                        <div class="bs-stepper-header">
                            <div class="step" data-target="#site-details">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-circle"><i class=" ti ti-world"></i></span>
                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">{{ __('Developer Setting') }}</span>
                                        <span class="bs-stepper-subtitle">{{ __('Add Developer Setting') }} </span>
                                    </span>
                                </button>
                            </div>
                        </div>
                        <div class="bs-stepper-content">
                            <!-- site Details -->
                            <div id="site-details" class="content">
                                <div class="row g-3">
                                    @if (in_array(auth()->user()->type, $access_all_type) &&
                                            in_array(auth()->user()->id, $access_all_id) &&
                                            auth()->user()->can($access_all_perms))
                                        <div class="col-md-6 ">
                                            <label class="form-label" for="website">{{ __('Website') }}</label>
                                            {!! html()->select('website', showType(), $setting['website'] ?? null)->class('select2 form-select')->required()->id('website') !!}
                                        </div>
                                        <div class="col-md-6 ">
                                            <label class="form-label" for="is_web">{{ __('Is Web') }}</label>
                                            {!! html()->select('is_web', showType(), $setting['is_web'] ?? null)->class('select2 form-select')->required()->id('is_web') !!}
                                        </div>
                                        <div class="col-md-6 ">
                                            <label class="form-label" for="is_vue">{{ __('Is Vue') }}</label>
                                            {!! html()->select('is_vue', showType(), $setting['is_vue'] ?? null)->class('select2 form-select')->required()->id('is_vue') !!}
                                        </div>


                                        <div class="col-md-6 ">
                                            <label class="form-label" for="additions">{{ __('Additions') }}</label>
                                            {!! html()->select('additions', showType(), $setting['additions'] ?? null)->class('select2 form-select')->required()->id('additions') !!}
                                        </div>
                                        <div class="col-md-6 ">
                                            <label class="form-label" for="is_return">{{ __('Return') }}</label>
                                            {!! html()->select('is_return', showType(), $setting['is_return'] ?? null)->class('select2 form-select')->required()->id('is_return') !!}
                                        </div>
                                        <div class="col-md-6 ">
                                            <label class="form-label"
                                                for="is_delivery_times">{{ __('Delivery Times') }}</label>
                                            {!! html()->select('is_delivery_times', showType(), $setting['is_delivery_times'] ?? null)->class('select2 form-select')->required()->id('is_delivery_times') !!}
                                        </div>
                                        <div class="col-md-6 ">
                                            <label class="form-label" for="size_guide">{{ __('Size Guide') }}</label>
                                            {!! html()->select('size_guide', showType(), $setting['size_guide'] ?? null)->class('select2 form-select')->required()->id('size_guide') !!}
                                        </div>
                                        <div class="col-md-6 ">
                                            <label class="form-label" for="social_login">{{ __('Social Login') }}</label>
                                            {!! html()->select('social_login', showType(), $setting['social_login'] ?? null)->class('select2 form-select')->required()->id('social_login') !!}
                                        </div>
                                        <div class="col-md-6 ">
                                            <label class="form-label" for="primary_login">{{ __('Primary Login') }}</label>
                                            {!! html()->select('primary_login', primaryLoginType(), $setting['primary_login'] ?? null)->class('select2 form-select')->required()->id('primary_login') !!}
                                        </div>
                                        <div class="col-md-6 ">
                                            <label class="form-label"
                                                for="product_table_ajax">{{ __('Product Table Ajax') }}</label>
                                            {!! html()->select('product_table_ajax', showType(), $setting['product_table_ajax'] ?? null)->class('select2 form-select')->required()->id('product_table_ajax') !!}
                                        </div>
                                        <div class="col-md-6 ">
                                            <label class="form-label"
                                                for="clothes_repeater">{{ __('Clothes Repeater') }}</label>
                                            {!! html()->select('clothes_repeater', showType(), $setting['clothes_repeater'] ?? null)->class('select2 form-select')->required()->id('clothes_repeater') !!}
                                        </div>
                                        <div class="col-md-6 ">
                                            <label class="form-label" for="amount">{{ __('Quantity') }}</label>
                                            {!! html()->select('amount', showType(), $setting['amount'] ?? null)->class('select2 form-select')->required()->id('amount') !!}
                                        </div>
                                        <div class="col-md-6 ">
                                            <label class="form-label" for="services">{{ __('services') }}</label>
                                            {!! html()->select('services', showType(), $setting['services'] ?? null)->class('select2 form-select')->required()->id('services') !!}
                                        </div>
                                        <div class="col-md-6 ">
                                            <label class="form-label" for="products_prices">{{ __('Update Products Prices From Excel') }}</label>
                                            {!! html()->select('products_prices', showType(), $setting['products_prices'] ?? null)->class('select2 form-select')->required()->id('products_prices') !!}
                                        </div>
                                        <div class="col-md-6 ">
                                            <label class="form-label" for="show_branch_stats">{{ __('Show Branch Statistics') }}</label>
                                            {!! html()->select('show_branch_stats', showType(), $setting['show_branch_stats'] ?? null)->class('select2 form-select')->required()->id('show_branch_stats') !!}
                                        </div>
                                    @endif
                                    {{-- <div class="col-md-6 ">
                                        <label class="form-label" for="site_open">{{ __('Site Country') }}</label>
                                        {!! Form::select('site_country', $countries, $setting['site_country'] ?? null, [
                                            'class' => 'select2 form-select',
                                            'required' => '',
                                            'id' => 'site_country',
                                        ]) !!}
                                    </div> --}}
                                </div>
                            </div>
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
