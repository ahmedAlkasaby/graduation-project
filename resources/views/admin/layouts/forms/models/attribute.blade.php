<div class="form-group">
    @if(!isset($label_show))
    <label>{{ __('Attribute') }}</label>
    @endif
    {!! html()->select('attribute_id', $attributes, $attribute_id ?? null)->class('select2')->required()->attribute('style', 'width: 100%') !!}
</div>
