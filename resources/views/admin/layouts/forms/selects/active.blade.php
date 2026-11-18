<div class="form-group">
    @if(!isset($label_show))
    <label>{{ $label_name ?? __('Status') }}</label>
    @endif
    {!! html()->select('active', statusType(), null)->class('select2')->required() !!}
</div>
