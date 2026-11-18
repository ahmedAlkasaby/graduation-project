@php
$add_class = '';
if (isset($text_class)) {
    $add_class = $text_class;
}
$field_name = 'phone';
if (isset($text_name)) {
    $field_name = $text_name;
}
$array_control = ['class' => 'form-control ' . $add_class, 'data-parsley-trigger' => 'input', 'data-parsley-minlength' => $minlength ?? '10', 'disabled' => $disable ?? false, 'readonly' => $read_only ?? false, 'data-parsley-type' => $text_type ?? 'number', 'data-parsley-pattern' => $pattern ?? '^0[0-99][0-9]{7,8}'];
if (!isset($not_req)) {
    $array_control['required'] = '';
}
if (isset($text_id)) {
    $array_control['id'] = $text_id;
}
@endphp
@include('admin.layouts.forms.fields.form-group-head', ['field_name' => $field_name])
@include('admin.layouts.forms.fields.label',['label_default'=>__("Phone")])
{!! html()->text($field_name, $text_value ?? null)->attributes($array_control) !!}
@include('admin.layouts.forms.fields.form-group-foot', ['field_name' => $field_name])
