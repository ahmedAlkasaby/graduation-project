<div class="form-group">
    <label> {{  __('Order No') }} </label>
    {!! html()->text('order_id', null)->class('form-control')->attribute('data-parsley-type', "integer") !!}
</div>

