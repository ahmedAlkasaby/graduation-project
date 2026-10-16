<div class="form-group">
        <label>{{ __('User Limit') }}</label>
        {!! html()->text('user_limit', null)->class('form-control')->attribute('data-parsley-type', "integer") !!}
</div>
