<div class="form-group">
        <label>{{ $max_discount ?? __('Max Discount') }}</label>
        {!! html()->text($discount_text ?? 'max_discount', $discount_value ?? null)->class('form-control')->attribute('data-parsley-type', $price_type ?? "number") !!}
</div>

