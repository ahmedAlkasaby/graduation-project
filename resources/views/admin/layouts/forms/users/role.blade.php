<div class="form-group">
    @include('admin.layouts.forms.fields.label', [
        'label_default' => __('Roles'),
    ])
    {!! html()->select('roles[]', $roles, $userRoles ?? null)->class('select2')->multiple()->attribute('style', 'width: 100%') !!}
</div>
