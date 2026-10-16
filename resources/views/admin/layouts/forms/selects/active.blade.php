<div class="form-group">
    @if(!isset($label_show))
    <label>{{ __('Status') }}</label>
    @endif
    {!! html()->select('active', statusType(), null)->class('select2')->required() !!}
</div>
