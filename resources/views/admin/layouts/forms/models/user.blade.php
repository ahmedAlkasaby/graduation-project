<div class="form-group">
    @if(!isset($label_show))
    <label>{{ __('User') }}</label>
    @endif
    {!! html()->select('user_id', $users, $user_id ?? null)->class('select2')->required() !!}
</div>
