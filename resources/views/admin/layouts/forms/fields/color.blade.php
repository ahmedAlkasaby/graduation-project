@php
$add_class = $text_class ?? '';

$field_name = $text_name ?? "color";

$array_control = [
'class' => 'form-control ' . $add_class,
'type' => 'color',
];
if (!empty($disable)) {
$array_control['disabled'] = 'disabled';
}

if (!isset($not_req) || $not_req == false) {
$array_control['required'] = '';
}
if (isset($placeholder)) {
$array_control['placeholder'] = $placeholder;
}
if (isset($text_id)) {
$array_control['id'] = $text_id;
}

$defaultColor = '#000000';
$value = $text_value ?? old($field_name) ?? $defaultColor;
@endphp

@include('admin.layouts.forms.fields.form-group-head',['field_name'=>$field_name])
@include('admin.layouts.forms.fields.label',[
'label_default'=> $label_name ?? __("Color"),
'label_req' => $label_req ?? false
])
{!! html()
->input('color', $field_name, $value)
->attributes($array_control)
!!}
@include('admin.layouts.forms.fields.form-group-foot',['field_name'=>$field_name])