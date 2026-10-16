<div class="form-group">
    @if(!isset($label_show))
    <label>{{ $label_text ?? __('Additions') }}</label>
    @endif
    @if(!isset($not_req))
    {!! html()->select('additions[]', $additions ?? null, $product_addition ?? null)->class('select2')->multiple()->required()->attribute('style', 'width: 100%') !!}
    @else
    {!! html()->select('additions[]', $additions ?? null, $product_addition ?? null)->class('select2')->multiple()->attribute('style', 'width: 100%') !!}
    @endif
</div>
