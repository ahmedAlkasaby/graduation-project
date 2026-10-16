<div class="form-group">
    @if(!isset($label_show))
    <label>{{ __('Role') }}</label>
    @endif
    {!! html()->select('role_id', $roles, $role_id ?? null)->class('select2')->required()->attribute('style', 'width: 100%') !!}
</div>
