@php
$add_class = $multiple = $select2_class = '';
$field_name = 'tags';
if (isset($select_class)) {
    $add_class = $select_class;
}
if (isset($select_name)) {
    $field_name = $select_name;
}
if (isset($read_only)) {
    $add_class .= ' select2-readonly';
}
if (!isset($select2)) {
    $select2_class = "js-example-tags";
}
$array_control = ['class' =>'form-control '.$select2_class .' ' . $add_class, 'data-parsley-trigger' => 'select', 'disabled' => $disable ?? false, 'readonly' => $read_only ?? false, 'style' => 'width: 100%'];
if (!isset($not_req)) {
    $array_control['required'] = '';
}
$array_control['multiple'] = '';

if (isset($select_id)) {
    $array_control['id'] = $select_id;
}
@endphp
@include('admin.layouts.forms.fields.form-group-head', ['field_name' => $field_name])
@include('admin.layouts.forms.fields.label',['label_default'=>__("Services")])
{!! html()->select($field_name.'[]', is_array($select_function ?? null) ? $select_function : statusType(), $select_value ?? null)->attributes($array_control) !!}
@include('admin.layouts.forms.fields.form-group-foot', ['field_name' => $field_name])
