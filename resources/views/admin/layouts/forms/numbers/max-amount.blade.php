<div class="form-group">
    <label class="form-label">{{ $max_amount ?? __('Max Amount') }}</label>
    {!! html()->text('max_amount', $max_amount_value ?? null)->class('form-control')->attribute('data-parsley-type', "number") !!}
</div>

