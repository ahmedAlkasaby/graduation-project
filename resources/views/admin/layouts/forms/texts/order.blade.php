<div class="form-group">
        <label> {{  __('Order ID') }} </label>
        {!! html()->text('order_id', null)->class('form-control')->attribute('data-parsley-type', "integer") !!}
    </div>
