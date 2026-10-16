<div class="form-group">
        <label>{{ __('Total Uses Limit') }}</label>
        {!! html()->text('count_used', null)->class('form-control')->attribute('data-parsley-type', "integer") !!}
    </div>
