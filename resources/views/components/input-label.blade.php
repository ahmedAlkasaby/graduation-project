@props([
    'value' => '',
    'required' => false,
])

<label {{ $attributes->merge(['class' => 'block label-bold']) }}>
    <b>
        {{ $value ?? $slot }}
        @if ($required)
            <span class="text-danger"> * </span>
        @endif
    </b>
</label>
