<!-- Default Wizard -->
<div class="col-12 mb-4">
    <small class="text-light fw-medium"></small>
    <div class="bs-stepper wizard-numbered mt-2">
        <div class="bs-stepper-header">
            <div class="step" data-target="#english-details">
                <button type="button" class="step-trigger">
                    <span class="bs-stepper-circle">{{ __('En') }}</span>
                    <span class="bs-stepper-label">
                        <span class="bs-stepper-title">{{ __('English Details') }} </span>
                        <span class="bs-stepper-subtitle">{{ __('Add English Details') }} </span>
                    </span>
                </button>
            </div>
            <div class="line">
                <i class="ti ti-chevron-right"></i>
            </div>
            <div class="step" data-target="#arabic-details">
                <button type="button" class="step-trigger">
                    <span class="bs-stepper-circle">{{ __('Ar') }}</span>
                    <span class="bs-stepper-label">
                        <span class="bs-stepper-title">{{ __('Arabic Details') }} </span>
                        <span class="bs-stepper-subtitle">{{ __('Add Arabic Details') }}</span>
                    </span>
                </button>
            </div>
        </div>
        <div class="bs-stepper-content">
            {{--  <form onSubmit="return false" data-parsley-validate >  --}}
            <!-- english Details -->
            <div id="english-details" class="content">
                <div class="row g-3">
                    @if ($show_name ?? false)
                        <div class="col-sm-12">
                            @include('admin.layouts.forms.fields.text', [
                                'text_name' => 'name[en]',
                                'text_value' => $name_en ?? null,
                                'label_name' => __('English Name'),
                                'not_req' => $not_req ?? true,

                            ])
                        </div>
                    @endif
                    @if ($show_value ?? false)
                        <div class="col-sm-12">
                            @include('admin.layouts.forms.fields.text', [
                                'text_name' => 'value[en]',
                                'text_value' => $value_en ?? null,
                                'label_name' => __('English Value'),
                                'not_req' => true,
                            ])
                        </div>
                    @endif

                    @if ($show_title ?? false)
                        <div class="col-sm-12">
                            @include('admin.layouts.forms.fields.text', [
                                'text_name' => 'title[en]',
                                'text_value' => $title_en ?? null,
                                'label_name' => __('English Title'),
                                'not_req' => $not_req ?? true,

                            ])
                        </div>
                    @endif
                    @if ($show_content ?? false)
                        <div class="col-sm-12">
                            @include('admin.layouts.forms.fields.textarea', [
                                'text_name' => 'content[en]',
                                'text_value' => $content_en ?? null,
                                'label_name' => __('English Content'),
                                'not_req' => $not_req ?? true,
                                'text_id' => 'my-textarea',
                            ])
                        </div>
                    @endif
                    @if ($show_address ?? false)
                        <div class="col-sm-12">
                            @include('admin.layouts.forms.fields.textarea', [
                                'text_name' => 'address[en]',
                                'text_value' => $address_en ?? null,
                                'label_name' => __('English Address'),
                                'not_req' => true,
                                'text_id' => 'my-textarea',
                            ])
                        </div>
                    @endif

                </div>
            </div>

            <!-- arabic-details -->
            <div id="arabic-details" class="content">
                <div class="row g-3">
                    @if ($show_name ?? false)
                        <div class="col-sm-12">
                            @include('admin.layouts.forms.fields.text', [
                                'text_name' => 'name[ar]',
                                'text_value' => $name_ar ?? null,
                                'label_name' => __('Arabic Name'),
                                'not_req' => $not_req ?? true,
                            ])
                        </div>
                    @endif
                    @if ($show_value ?? false)
                        <div class="col-sm-12">
                            @include('admin.layouts.forms.fields.text', [
                                'text_name' => 'value[ar]',
                                'text_value' => $value_ar ?? null,
                                'label_name' => __('Arabic Value'),
                                'not_req' => true,
                            ])
                        </div>
                    @endif

                    @if ($show_title ?? false)
                        <div class="col-sm-12">
                            @include('admin.layouts.forms.fields.text', [
                                'text_name' => 'title[ar]',
                                'text_value' => $title_ar ?? null,
                                'label_name' => __('Arabic Title'),
                                'not_req' => $not_req ?? true,
                            ])
                        </div>
                    @endif
                    @if ($show_content ?? false)
                        <div class="col-sm-12">
                            @include('admin.layouts.forms.fields.textarea', [
                                'text_name' => 'content[ar]',
                                'text_value' => $content_ar ?? null,
                                'label_name' => __('Arabic Content'),
                                'not_req' => $not_req ?? true,
                                'text_id' => 'my-textarea',
                            ])
                        </div>
                    @endif
                    @if ($show_address ?? false)
                        <div class="col-sm-12">
                            @include('admin.layouts.forms.fields.textarea', [
                                'text_name' => 'address[ar]',
                                'text_value' => $address_ar ?? null,
                                'label_name' => __('Arabic Address'),
                                'not_req' => true,
                                'text_id' => 'my-textarea',
                            ])
                        </div>
                    @endif

                </div>
            </div>
