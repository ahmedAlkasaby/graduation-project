@props([
    'disabled' => false,
    'required' => false,
    'placeholder' => '',
    'value' => '',
    'name' => '',
    'type' => 'text',
    'class' => '',
])

<input type="{{ $type }}" value="{{ $value }}" {{ $disabled ? 'disabled' : '' }}
    {{ $required ? 'required' : '' }} placeholder="{{ $placeholder }}" name="{{ $name }}"
    {!! $attributes->merge([
        'class' => 'form-control ' . $class,
    ]) !!}>
