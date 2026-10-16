<div class="form-group phones-group phone-container {{ $phone_show == 'phone' ? '': 'hide' }}">
    @if (!isset($label_show))
        <label>{{ $label_name ?? __('Phone') }}</label>
    @endif
    @php
        $add_class = '';
        if (isset($text_class)) {
            $add_class = $text_class;
        }
        $array_control = ['class' => 'form-control ' . $add_class, 'data-parsley-trigger' => 'input', 'data-parsley-minlength' => $minlength ?? '11', 'disabled' => $disable ?? false, 'readonly' => $read_only ?? false, 'data-parsley-type' => $text_type ?? 'number', 'data-parsley-pattern' => $pattern ?? '^01[0-2,5][0-9]{8}'];
        if (!isset($not_req)) {
            $array_control['required'] = '';
        }
        if (isset($text_id)) {
            $array_control['id'] = $text_id;
        }
    @endphp
    {!! html()->text($text_name ?? 'phone', $text_value ?? null)->attributes($array_control) !!}
</div>

<div class="form-group phones-group international-phone-container {{ $phone_show == 'international' ? '': 'hide' }}">
    @if (!isset($label_show))
        <label>{{ $label_name ?? __('International Phone') }}</label>
    @endif
    @php
        $add_class = '';
        if (isset($text_class)) {
            $add_class = $text_class;
        }
        $array_control = ['class' => 'form-control ' . $add_class, 'data-parsley-trigger' => 'input', 'data-parsley-minlength' => $minlength ?? '11', 'disabled' => $disable ?? false, 'readonly' => $read_only ?? false, 'data-parsley-type' => $text_type ?? 'number', 'data-parsley-pattern' => $pattern ?? '^00[0-999][0-9]{9,12}'];
        if (!isset($not_req)) {
            $array_control['required'] = '';
        }
        if (isset($text_id)) {
            $array_control['id'] = $text_id;
        }
    @endphp
    {!! html()->text($text_name ?? 'phone', $text_value ?? null)->attributes($array_control) !!}
</div>

<div class="form-group phones-group land-phone-container {{ $phone_show == 'land_line' ? '': 'hide' }}">
    @if (!isset($label_show))
        <label>{{ $label_name ?? __('Land Phone') }}</label>
    @endif
    @php
        $add_class = '';
        if (isset($text_class)) {
            $add_class = $text_class;
        }
        $array_control = ['class' => 'form-control ' . $add_class, 'data-parsley-trigger' => 'input', 'data-parsley-minlength' => $minlength ?? '11', 'disabled' => $disable ?? false, 'readonly' => $read_only ?? false, 'data-parsley-type' => $text_type ?? 'number', 'data-parsley-pattern' => $pattern ?? '^0[0-99][0-9]{7,8}'];
        if (!isset($not_req)) {
            $array_control['required'] = '';
        }
        if (isset($text_id)) {
            $array_control['id'] = $text_id;
        }
    @endphp
    {!! html()->text($text_name ?? 'phone', $text_value ?? null)->attributes($array_control) !!}
</div>
