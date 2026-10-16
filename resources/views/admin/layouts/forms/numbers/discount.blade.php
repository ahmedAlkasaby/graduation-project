<div class="form-group">
        <label>{{ $discount ?? __('Discount') }}</label>
        {!! html()->text($discount_text ?? 'discount', $discount_value ?? null)->class('form-control')->attribute('data-parsley-min', '1')->attribute('data-parsley-max', '100')->required() !!}
</div>

