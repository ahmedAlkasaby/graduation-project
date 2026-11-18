<div class="form-group">
    @if(!isset($label_show))
    <label>{{ __('Categories') }}</label>
    @endif
    @if(!isset($not_req))
    {!! html()->select('categories[]', $categories, $product_category)->class('select2')->multiple()->required()->attribute('style', 'width: 100%') !!}
    @else
    {!! html()->select('categories[]', $categories, $product_category)->class('select2')->multiple()->attribute('style', 'width: 100%') !!}
    @endif
</div>
