<div class="form-group">
    <label>{{ $order_limit ?? __('Order Limit') }}</label>
    {!! html()->text('order_limit', $order_limit_value ?? null)->class('form-control')->attribute('data-parsley-type', "number") !!}
</div>

