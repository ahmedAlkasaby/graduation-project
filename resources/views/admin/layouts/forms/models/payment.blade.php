<div class="form-group">
    @if(!isset($label_show))
    <label>{{ __('Payment') }}</label>
    @endif
    {!! html()->select('payment_id', $payments, $payment_id ?? null)->class('select2')->required()->attribute('style', 'width: 100%') !!}
</div>
