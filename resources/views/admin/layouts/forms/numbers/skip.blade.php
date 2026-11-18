<div class="form-group">
        <label>{{ $skip ?? __('Skip') }}</label>
        {!! html()->text('skip', $skip_value ?? null)->class('form-control')->attribute('data-parsley-type', "number") !!}
    </div>
