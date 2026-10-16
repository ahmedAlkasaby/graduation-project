<div class="form-group">
        <label>{{ __('English Content') }}</label>
    {!! html()->textarea('content_en', $content_en ?? "")->class('form-control')->id('my-textarea') !!}
    </div>
