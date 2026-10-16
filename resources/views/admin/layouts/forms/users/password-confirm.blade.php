<div class="form-group">
    @include('admin.layouts.forms.fields.label',['label_default'=>__("Confirm Password")])
    @if($new > 0)
    {!! html()->password('confirm-password')->class('form-control')->id('password-confirm')->required()->attribute('data-parsley-minlength', '8')->attribute('data-parsley-equalto', '#password') !!}
    @else
    {!! html()->password('confirm-password')->class('form-control')->id('password-confirm')->attribute('data-parsley-minlength', '8')->attribute('data-parsley-equalto', '#password') !!}
    @endif

    <!-- <span toggle="#password-confirm" class="fa fa-fw fa-eye field-icon toggle-password"></span> -->

</div>
