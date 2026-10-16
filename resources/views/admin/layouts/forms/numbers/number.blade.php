<div class="form-group">
        <label>{{ $price ?? __('Price') }}</label>
        @if(!isset($not_req))
        {!! html()->text($price_text ?? 'price', $price_value ?? null)->class('form-control')->required()->attribute('data-parsley-type', $price_type ?? "number") !!}
        @else
        {!! html()->text($price_text ?? 'price', $price_value ?? null)->class('form-control')->attribute('data-parsley-type', $price_type ?? "number") !!}
        @endif
    </div>

