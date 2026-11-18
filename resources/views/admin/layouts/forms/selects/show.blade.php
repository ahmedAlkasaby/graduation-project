<div class="form-group">
    @if(!isset($label_show))
    <label>{{ $show_name ?? __('Show') }}</label>
    @endif
    @if(!isset($not_req))
    {!! html()->select($show_text ?? 'show', $show_function ?? showType(), $show_value ?? null)->class('select2')->attribute('style', 'width: 100%')->required() !!}
    @else
    {!! html()->select($show_text ?? 'show', $show_function ?? showType(), $show_value ?? null)->class('select2')->attribute('style', 'width: 100%') !!}
    @endif
</div>
