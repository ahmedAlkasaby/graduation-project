@php
$add_class = '';
if (isset($number_class)) {
    $add_class = $number_class;
}
$field_name = 'price';
if (isset($number_name)) {
    $field_name = $number_name;
}
@endphp
@include('admin.layouts.forms.fields.form-group-head', ['field_name' => $field_name])
@include('admin.layouts.forms.fields.label',['label_default'=>__("Price")])
@if (!isset($not_req))
    {!! html()->text($field_name, $number_value ?? null)->attributes([
        'class' => 'form-control ' . $add_class,
        'id' => $number_id ?? '',
        'data-parsley-min' => $min ?? 0,
        'data-parsley-trigger' => 'input',
        'disabled' => $disable ?? false,
        'readonly' => $read_only ?? false,
        'data-parsley-type' => $number_type ?? 'number',
        'required' => '',
    ]) !!}
@else
    {!! html()->text($field_name, $number_value ?? null)->attributes([
        'class' => 'form-control ' . $add_class,
        'id' => $number_id ?? '',
        'data-parsley-min' => $min ?? 0,
        'data-parsley-trigger' => 'input',
        'disabled' => $disable ?? false,
        'readonly' => $read_only ?? false,
        'data-parsley-type' => $number_type ?? 'number',
    ]) !!}
@endif
@include('admin.layouts.forms.fields.form-group-foot', ['field_name' => $field_name])
