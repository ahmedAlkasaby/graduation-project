<div class="form-group">
        <label>{{ __('Name') }}</label>
        {!! html()->text('value', null)->class('form-control')->required()->attribute('data-parsley-minlength', '5') !!}
    </div>
