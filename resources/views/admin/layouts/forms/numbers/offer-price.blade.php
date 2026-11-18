<div class="form-group">
    <label>{{ $offer_price ?? __('Price Before Offer') }}</label>
    {!! html()->text('offer_price', $offer_price_value ?? null)->class('form-control')->attribute('data-parsley-type', "number") !!}
</div>

