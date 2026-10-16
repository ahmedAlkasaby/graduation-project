@php
$add_class = '';
if (isset($text_class)) {
    $add_class = $text_class;
}
$field_name = 'confirm-password';
if (isset($text_name)) {
    $field_name = $text_name;
}
@endphp
@include('admin.layouts.forms.fields.form-group-head', ['field_name' => $field_name])
    @if (!isset($label_show))
        <label>{{ $label_name ?? __('Confirm Password') }}</label>
    @endif
    @if (!isset($not_req))
        {!! html()->text($field_name, '')->attributes([
            'class' => 'form-control' . $add_class,
            'data-parsley-minlength' => $minlength ?? '8',
            'id' => $text_id ?? '',
            'data-parsley-trigger' => 'input',
            'disabled' => $disable ?? false,
            'readonly' => $read_only ?? false,
            'data-parsley-equalto' => $equalto ?? '#password',
            'required' => '',
        ]) !!}
    @else
        {!! html()->text($field_name, '')->attributes([
            'class' => 'form-control' . $add_class,
            'data-parsley-minlength' => $minlength ?? '8',
            'id' => $text_id ?? '',
            'data-parsley-trigger' => 'input',
            'disabled' => $disable ?? false,
            'readonly' => $read_only ?? false,
            'data-parsley-equalto' => $equalto ?? '#password',
        ]) !!}
    @endif
    @include('admin.layouts.forms.fields.form-group-foot', ['field_name' => $field_name])
