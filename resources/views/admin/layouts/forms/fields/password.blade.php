@php
    $field_name = $text_name ?? 'password';
    $array_control = [
        'class' => trim('form-control ' . ($text_class ?? '')),
        'data-parsley-minlength' => $minlength ?? '8',
        'data-parsley-trigger' => 'input',
        'autocomplete' => 'new-password',
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
    if (isset($text_id)) {
        $array_control['id'] = $text_id;
    }
    if (isset($equalto)) {
        $array_control['data-parsley-equalto'] = $equalto;
    }
@endphp
@include('admin.layouts.forms.fields.form-group-head', ['field_name' => $field_name])
@include('admin.layouts.forms.fields.label', ['label_default' => __('Password')])
{{-- Never prefilled: an edit form leaves it empty to keep the current password. --}}
{!! html()->password($field_name)->attributes($array_control) !!}
@include('admin.layouts.forms.fields.form-group-foot', ['field_name' => $field_name])
