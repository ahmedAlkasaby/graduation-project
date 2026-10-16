<div class="form-group">
    @if(!isset($label_show))
    <label>{{ __('Admin Notifi') }}</label>
    @endif
    {!! html()->select('is_notify', statusShow(), null)->class('select2')->required() !!}
</div>
