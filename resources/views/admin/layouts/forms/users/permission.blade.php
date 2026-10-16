<div class="form-group">
    <label>{{ __('Permissions') }}</label>
    {!! html()->select('permission[]', $permission, $rolePermissions)->class('select2')->multiple() !!}
</div>
