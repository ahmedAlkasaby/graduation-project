<div class="form-group">
        <label>{{  __('Point') }}</label>
        {!! html()->text('point', null)->class('form-control')->attribute('data-parsley-type', "number")->required() !!}
    </div>
