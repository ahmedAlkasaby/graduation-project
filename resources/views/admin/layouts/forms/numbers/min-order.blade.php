<div class="form-group">
        <label>{{ $min_order ?? __('Minimum order') }}</label>
        {!! html()->text($order_text ?? 'min_order', $order_value ?? null)->class('form-control')->attribute('data-parsley-type', $price_type ?? "number") !!}
</div>

