<div class="form-group">
    <label>{{ $shipping ?? __('Shipping') }}</label>
    {!! html()->text($shipping_text ?? 'shipping', $shipping_value ?? null)->class('form-control')->attribute('data-parsley-type', "number") !!}
</div>
