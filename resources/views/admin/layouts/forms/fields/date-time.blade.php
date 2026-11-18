@php
    $add_class = 'datepicker';
    if (isset($date_class)) {
        $add_class = $date_class;
    }
    $date_time_id = 'datetimepicker';
    if (isset($date_id)) {
        $date_time_id = $date_id;
    }
    $array_control = [
        'class' => 'form-control datetimepicker-input' . $add_class,
        'id' => $date_time_id,
        'data-toggle' => 'datetimepicker',
        'data-target' => $date_time_id,
        'data-parsley-trigger' => 'input',
        'disabled' => $disable ?? false,
        'readonly' => $read_only ?? false,
    ];
    if (!isset($not_req)) {
        $array_control['required'] = '';
    }
    if (isset($placeholder)) {
        $array_control['placeholder'] = $placeholder;
    }

    $field_name = 'date_at';
    if (isset($date_name)) {
        $field_name = $date_name;
    }
@endphp
@include('admin.layouts.forms.fields.form-group-head', ['field_name' => $field_name])
@include('admin.layouts.forms.fields.label', ['label_default' => __('Date')])
{{--  hijri-date-input  --}}
{!! html()->text($field_name, old($field_name, $date_value ?? null))->attributes($array_control) !!}

<div class="input-group-append" data-target="#{{ $date_time_id }}" data-toggle="datetimepicker">
    <div class="input-group-text">
        <i class="icon-note-text"></i>
    </div>
</div>
@include('admin.layouts.forms.fields.form-group-foot', ['field_name' => $field_name])
