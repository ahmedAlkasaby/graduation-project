
@php
    $repeater_list = $repeater_list ?? 'rows';
    $repeater_field = $repeater_field ?? 'value';
    $repeater_class = $repeater_class ?? $repeater_list . '-repeater';
    $repeater_width = $repeater_width ?? 10;

    $repeater_rows = old($repeater_list);

    if (! is_array($repeater_rows)) {
        $repeater_rows = collect($repeater_values ?? [])
            ->map(fn($value) => [$repeater_field => $value])
            ->all();
    }

    if (empty($repeater_rows)) {
        $repeater_rows = [[$repeater_field => '']];
    }

    $repeater_control = [
        'class' => 'form-control',
        'data-parsley-trigger' => 'input',
        'placeholder' => $repeater_placeholder ?? $repeater_field_label ?? '',
    ];
@endphp

<label class="form-label fw-medium">{{ $repeater_label ?? __('Items') }}</label>

<div class="{{ $repeater_class }}">
    <div data-repeater-list="{{ $repeater_list }}">
        @foreach ($repeater_rows as $repeater_row)
            <div data-repeater-item class="row mb-3 align-items-end">
                <div class="col-md-{{ $repeater_width }}">
                    @include('admin.layouts.forms.fields.label', [
                        'label_default' => $repeater_field_label ?? __('Value'),
                        'label_name' => $repeater_field_label ?? null,
                    ])


                    {{ html()
                        ->text($repeater_field, (string) ($repeater_row[$repeater_field] ?? ''))
                        ->attributes($repeater_control)
                        ->forgetAttribute('id') }}
                </div>
                <div class="col-md-{{ 12 - $repeater_width }}">
                    @include('admin.layouts.forms.buttons.delete')
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-2">
        @include('admin.layouts.forms.buttons.add', ['add' => $repeater_add_label ?? __('Add')])
    </div>
</div>

@if ($repeater_js ?? true)
    @include('admin.layouts.forms.repeater-js', ['repeater_class' => $repeater_class])
@endif
