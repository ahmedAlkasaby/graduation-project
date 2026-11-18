<div class="form-group">
    @if(!isset($label_show))
    <label>{{ __('Vip') }}</label>
    @endif
    {!! html()->select('vip', statusShow(), null)->class('select2')->required() !!}
</div>
