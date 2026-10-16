<!-- Default Wizard -->
@include('admin.layouts.forms.fields.hidden', [
    'hidden' => 'previous_url',
    'hidden_id' => url()->previous(),
])
<div class="col-12 mb-4">
    <small class="text-light fw-medium"></small>
    <div class="bs-stepper wizard-numbered mt-2">
        <div class="bs-stepper-content">
            <form onSubmit="return false">
                <div class="row">
                    <div class="col-sm-6">
                        @include('admin.layouts.forms.fields.text', [
                            'label_req' => true,
                            'label_name' => __('Name First'),
                            'text_name' => 'name_first',
                        ])
                        @include('admin.layouts.forms.fields.text', [
                            'not_req' => true,
                            'label_name' => __('Email'),
                            'text_name' => 'email',
                            'text_type' => 'email',
                        ])
                        @include('admin.layouts.forms.users.password')
                        @include('admin.layouts.forms.fields.select')
                    </div>
                    <div class="col-sm-6">
                        @include('admin.layouts.forms.fields.text', [
                            'label_req' => true,
                            'label_name' => __('Name Last'),
                            'text_name' => 'name_last',
                        ])
                        @include('admin.layouts.forms.fields.phone')
                        @include('admin.layouts.forms.users.password-confirm')
                        @include('admin.layouts.forms.selects.locale')
                    </div>
                    <div class="content-header mb-3"></div>
                    <div class="col-sm-12">
                        @include('admin.layouts.forms.dropzone.dropzone', [
                            'name' => 'image',
                            'existingImageUrl' => isset($user) && $user->image ? asset($user->image) : null,
                        ]) </div>
                    <div class="col-sm-12">
                        {{-- Every panel user gets the same fields; only an admin may set someone's type. --}}
                        @if ($new > 0 || in_array(auth()->user()->type, $user_admins))
                            <div class="form-group">
                                @include('admin.layouts.forms.fields.label', [
                                    'label_default' => __('Type'),
                                ])
                                {!! html()->select('type', userType('yes', 'no'), $new > 0 ? null : $user->type)->class('select2 user-type-role')->required() !!}
                            </div>
                            <div class="user-all-role">
                                @include('admin.layouts.forms.users.role')
                            </div>
                            @include('admin.layouts.forms.fields.select', [
                                'select_name' => 'vip',
                                'label_name' => __('Vip'),
                                'select_function' => statusShow(),
                            ])
                            @include('admin.layouts.forms.fields.select', [
                                'select_name' => 'is_notify',
                                'label_name' => __('Admin Notify'),
                                'select_function' => statusShow(),
                            ])
                        @endif
                    </div>
                    <div class="content-header mb-3"></div>
                    <div class="col-12 d-flex justify-content-between">
                        <div class="col-12"> @include('admin.layouts.forms.buttons.form-save')</div>
                    </div>
            </form>
        </div>
    </div>
</div>
<!-- /Default Wizard -->
