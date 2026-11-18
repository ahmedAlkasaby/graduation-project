<div class="form-group">
    @if(!isset($label_show))
    <label>{{ __('Category') }}</label>
    @endif
    {!! html()->select('category_id', $categories, $category_id ?? null)->class('select2')->required()->attribute('style', 'width: 100%') !!}
</div>
