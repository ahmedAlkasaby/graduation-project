<div class="form-group">
    @if(!isset($label_show))
    <label>{{ $product ?? __('Product') }}</label>
    @endif
    {!! html()->select('post_id', $posts, $post_id ?? null)->class('select2')->required()->attribute('style', 'width: 100%') !!}
</div>
