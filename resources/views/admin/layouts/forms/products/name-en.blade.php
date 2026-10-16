<div class="form-group">
        <label>{{ __('English Name') }}</label>
    {!! html()->text('name_en', $name_en ?? "")->class('form-control')->required()->attribute('data-parsley-minlength', $minlength ?? '3') !!}
    </div>
