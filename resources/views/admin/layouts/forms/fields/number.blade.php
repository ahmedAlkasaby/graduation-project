@php
    $add_class = '';
    if (isset($number_class)) {
        $add_class = $number_class;
    }

    $field_name = 'price';
    if (isset($number_name)) {
        $field_name = $number_name;
    }
    $array_control = [
        'class' => 'form-control ' . $add_class,
        'data-parsley-trigger' => 'input',
        'data-parsley-type' => $number_type ?? 'number',
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
    if (isset($number_id)) {
        $array_control['id'] = $number_id;
    }
    if (isset($min)) {
        $array_control['data-parsley-min'] = $min;
    }
    if (isset($max)) {
        $array_control['data-parsley-max'] = $max;
    }
    if (isset($placeholder)) {
        $array_control['placeholder'] = $placeholder;
    }
    if (isset($range)) {
        $array_control['data-parsley-range'] = $range;
    }
    $label_default = __('site.' . $field_name);
    if (isset($label)) {
        $label_default = $label;
    }

@endphp
@include('admin.layouts.forms.fields.form-group-head', ['field_name' => $field_name])
@include('admin.layouts.forms.fields.label', ['label_default' => $label_default])
{{ html()->number($field_name, $number_value ?? null)->attributes($array_control) }}
@include('admin.layouts.forms.fields.form-group-foot', ['field_name' => $field_name])
