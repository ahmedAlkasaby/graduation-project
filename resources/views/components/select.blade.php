@props([
    'options' => null,
    'selected' => '',
    'name' => '',
    'class' => '',
])

<select class="form-control {{ $class }}" name="{{ $name }}">
    @if ($options)
        @foreach ($options as $key => $option)
            <option value="{{ $key }}" {{ $selected == $key ? 'selected' : '' }}>
                {{ $option }}
            </option>
        @endforeach
    @endif
</select>
