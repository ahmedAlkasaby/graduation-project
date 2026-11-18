<div class="form-group">
        <label>{{  __('Wallet') }}</label>
        {!! html()->text('wallet', null)->class('form-control')->attribute('data-parsley-type', "number")->required() !!}
    </div>
