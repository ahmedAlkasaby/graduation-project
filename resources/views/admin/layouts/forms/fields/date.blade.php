@php
$add_class = 'flatpickr-date';
if (isset($date_class)) {
$add_class = $date_class;
}
$array_control = [
'class' => 'form-control flatpickr-input ' . ($add_class ?? ''),
'id' => $date_name,
'data-parsley-trigger' => 'input',
];
if (!empty($disable)) {
$array_control['disabled'] = 'disabled';
}
if (!empty($read_only)) {
$array_control['readonly'] = 'readonly';
}
if (!isset($not_req)) {
$array_control['required'] = '';
}
if (isset($date_id)) {
$array_control['id'] = $date_id;
}
$field_name = 'date';
if (isset($date_name)) {
$field_name = $date_name;
}
@endphp
@include('admin.layouts.forms.fields.form-group-head', ['field_name' => $field_name])
@include('admin.layouts.forms.fields.label',['label_default'=>__("Date")])
<i class="ti ti-calendar calendar-icon" aria-hidden="true"></i>
{!! html()->text($field_name, $date_value ?? null)->attributes($array_control) !!}
@include('admin.layouts.forms.fields.form-group-foot', ['field_name' => $field_name])
