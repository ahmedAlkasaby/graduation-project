<div class="form-group">
     @include('admin.layouts.forms.fields.label',['label_default'=>__("User Uses")])
    {!! html()->text('use_count', $use_count ?? null)->class('form-control')->attribute('data-parsley-type', "integer") !!}
</div>
