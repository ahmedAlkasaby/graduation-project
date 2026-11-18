<div class="form-group">
        <label>{{ __('English Branch') }}</label>
    {!! html()->text('branch_en', $branch_en ?? "")->class('form-control')->required()->attribute('data-parsley-minlength', $minlength ?? '3') !!}
    </div>
