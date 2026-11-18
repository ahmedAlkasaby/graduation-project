<div class="form-group">
        <label>{{ __('Arabic Branch') }}</label>
    {!! html()->text('branch_ar', $branch_ar ?? "")->class('form-control')->required()->attribute('data-parsley-minlength', $minlength ?? '3') !!}
    </div>
