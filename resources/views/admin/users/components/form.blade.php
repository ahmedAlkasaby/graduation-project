{{--
    The user create / edit form. Every select list comes from
    UserService::formOptions(), the same data the API's form-options
    endpoint serves, so a type outranking the signed in admin is never offered.
--}}
<div class="col-12 mb-4">
    <div class="card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-sm-6">
                    @include('admin.layouts.forms.fields.text', [
                        'label_req' => true,
                        'label_name' => __('Full Name'),
                        'text_name' => 'full_name',
                    ])
                </div>
                <div class="col-sm-6">
                    @include('admin.layouts.forms.fields.phone', [
                        'label_req' => true,
                        'label_name' => __('Phone'),
                        'not_pattern' => true,
                    ])
                </div>
                <div class="col-sm-6">
                    @include('admin.layouts.forms.fields.text', [
                        'not_req' => true,
                        'label_name' => __('Email'),
                        'text_name' => 'email',
                        'text_type' => 'email',
                    ])
                    <small class="text-muted">{{ __('Required for admins') }}</small>
                </div>
                <div class="col-sm-6">
                    @include('admin.layouts.forms.fields.select', [
                        'label' => __('Type'),
                        'select_name' => 'type',
                        'select_id' => 'user-type',
                        'select_function' => $typeOptions,
                    ])
                </div>
                <div class="col-sm-12" id="user-roles-wrap" data-role-types='@json($rolesForTypes)'>
                    @include('admin.layouts.forms.fields.select', [
                        'not_req' => true,
                        'label' => __('Roles'),
                        'select_name' => 'roles',
                        'is_multiple' => true,
                        'select_function' => $roleOptions,
                        'select_value' => old('roles', isset($user) ? $user->roles->pluck('id')->all() : []),
                    ])
                </div>
                <div class="col-sm-6">
                    @include('admin.layouts.forms.fields.password', [
                        'label_name' => __('Password'),
                        'text_id' => 'password',
                        'not_req' => isset($user) ? true : null,
                    ])
                </div>
                <div class="col-sm-6">
                    @include('admin.layouts.forms.fields.password', [
                        'label_name' => __('Confirm Password'),
                        'text_name' => 'confirm_password',
                        'text_id' => 'confirm_password',
                        'equalto' => '#password',
                        'not_req' => isset($user) ? true : null,
                    ])
                </div>
                <div class="col-sm-4">
                    @include('admin.layouts.forms.fields.select', [
                        'label' => __('Language'),
                        'select_name' => 'locale',
                        'select_function' => $localeOptions,
                    ])
                </div>
                <div class="col-sm-4">
                    @include('admin.layouts.forms.fields.select', [
                        'not_req' => true,
                        'label' => __('Gender'),
                        'select_name' => 'gender',
                        'select_function' => $genderOptions,
                    ])
                </div>
                <div class="col-sm-4">
                    @include('admin.layouts.forms.fields.date', [
                        'not_req' => true,
                        'label_name' => __('Birth Date'),
                        'date_name' => 'birth_date',
                        'date_value' => old('birth_date', isset($user) ? $user->birth_date?->format('Y-m-d') : null),
                    ])
                </div>
                <div class="col-sm-6">
                    @include('admin.layouts.forms.fields.select', [
                        'label' => __('Status'),
                        'select_name' => 'active',
                    ])
                </div>
                <div class="col-sm-6">
                    @include('admin.layouts.forms.fields.select', [
                        'label' => __('Notifications'),
                        'select_name' => 'is_notify',
                        'select_function' => statusShow(),
                    ])
                </div>
                <div class="col-sm-12">
                    @include('admin.layouts.forms.dropzone.dropzone', [
                        'name' => 'image',
                        'existingImageUrl' => isset($user) && $user->image ? upload_url($user->image) : null,
                    ])
                </div>
                <div class="col-12">
                    @include('admin.layouts.forms.buttons.form-save')
                </div>
            </div>
        </div>
    </div>
</div>
