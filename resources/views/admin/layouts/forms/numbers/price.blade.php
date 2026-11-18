<div class="form-group">
        <label>{{ $price ?? __('Price') }}</label>
        {!! html()->text('price', $price_value ?? null)->class('form-control')->attribute('data-parsley-type', "number")->required() !!}
    </div>
