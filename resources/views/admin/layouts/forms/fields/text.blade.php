@php
    $add_class = '';
    $text_readonly = false;
    if (isset($text_class)) {
        $add_class = $text_class;
    }
    $field_name = 'name';
    if (isset($text_name)) {
        $field_name = $text_name;
    }
    $array_control = [
        'class' => 'form-control ' . $add_class,
        'data-parsley-trigger' => 'input',
        'type' => $date_type ?? 'text',
    ];
    if (isset($disable)) {
        $array_control['disabled'] = true;
    }
    if (isset($read_only)) {
        $array_control['readonly'] = true;
    }
    if (!isset($not_req)) {
        $array_control['required'] = '';
    }
    if (isset($text_type) && $text_type != '') {
        $array_control['data-parsley-type'] = $text_type;
    }
    if (isset($placeholder)) {
        $array_control['placeholder'] = $placeholder;
    }
    if (isset($text_id)) {
        $array_control['id'] = $text_id;
    }
@endphp
@include('admin.layouts.forms.fields.form-group-head', ['field_name' => $field_name])
@include('admin.layouts.forms.fields.label', ['label_default' => __('Name')])
{{ html()->text($field_name, $text_value ?? null)->attributes($array_control) }}
@include('admin.layouts.forms.fields.form-group-foot', ['field_name' => $field_name])
{{-- for="{{$field_name}}" --}}
