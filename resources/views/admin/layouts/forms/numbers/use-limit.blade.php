<div class="form-group">
        <label>{{ __('Limit Per User') }}</label>
        {!! html()->text('use_limit', null)->class('form-control')->attribute('data-parsley-type', "integer") !!}
</div>
