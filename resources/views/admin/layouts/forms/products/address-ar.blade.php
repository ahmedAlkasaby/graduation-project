<div class="form-group">
        <label>{{ __('Arabic Address') }}</label>
    {!! html()->text('address_ar', $address_ar ?? "")->class('form-control')->required()->attribute('data-parsley-minlength', $minlength ?? '3') !!}
    </div>
