<div class="form-group">
        <label>{{ __('Arabic Content') }}</label>
    {!! html()->textarea('content_ar', $content_ar ?? "")->class('form-control')->id('my-textarea') !!}
    </div>
