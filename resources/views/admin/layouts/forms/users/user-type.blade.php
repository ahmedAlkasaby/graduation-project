<div class="form-group">
    <label>{{ __('Type') }}</label>
    @if($user_active > 0 )
    {!! html()->select('type', userType(), null)->class('select2 user-type-role') !!}
    @else
    {!! html()->select('type', userType(), null)->class('select2 user-type-role') !!}
    @endif
</div>
