<div class="form-group">
    <label>{{ __('Type') }}</label>
    {!! html()->select('type', userType(), null)->class('select2 user-type-role')->required() !!}
</div>
