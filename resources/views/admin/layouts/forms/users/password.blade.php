<div class="form-group">
    @include('admin.layouts.forms.fields.label',['label_default'=>__("Password")])
    @if($new > 0)
    {!! html()->password('password')->class('form-control')->id('password')->required()->attribute('data-parsley-minlength', '8') !!}
    @else
    {!! html()->password('password')->class('form-control')->id('password')->attribute('data-parsley-minlength', '8') !!}
    @endif
    <!-- <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span> -->
</div>
<div class="form-group">
    <div id="meter_wrapper">
        <div id="meter"></div>
    </div>
</div>

