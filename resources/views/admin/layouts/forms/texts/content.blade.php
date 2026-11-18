<div class="form-group">
    <label>{{ $content ?? __('Content') }}</label>
    {!! html()->textarea($content_text ?? 'content', $content_value ?? null)->class('form-control') !!}
</div>
