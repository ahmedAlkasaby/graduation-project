<div class="form-group">
        <label>{{ __('Arabic Name') }}</label>
    {!! html()->text('name_ar', $name_ar ?? "")->class('form-control')->required()->attribute('data-parsley-minlength', $minlength ?? '3') !!}
    </div>
