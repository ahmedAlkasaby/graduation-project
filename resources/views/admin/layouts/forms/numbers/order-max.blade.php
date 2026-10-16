<div class="form-group">
    <label>{{ $order_max ?? __('Max Per Order') }}</label>
    {!! html()->text('order_max', $order_max_value ?? null)->class('form-control')->attribute('data-parsley-type', "number") !!}
</div>

