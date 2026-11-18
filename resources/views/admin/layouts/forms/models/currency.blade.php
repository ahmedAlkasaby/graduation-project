<div class="form-group">
    @if(!isset($label_show))
    <label>{{ __('Currency') }}</label>
    @endif
    {!! html()->select('currency_id', $currencies, $currency_id ?? null)->class('select2')->required()->attribute('style', 'width: 100%') !!}
</div>
