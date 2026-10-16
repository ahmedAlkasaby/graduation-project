<div class="form-group">
    <label>{{ $date_label ?? __('Date') }}</label>

    @if (!isset($not_req))
        {!! html()->text($date_text ?? 'date', old($date_text ?? 'date', $date_value ?? null))->class($date_class ?? 'form-control datepicker')->required() !!}
    @else
        {!! html()->text($date_text ?? 'date', old($date_text ?? 'date', $date_value ?? null))->class($date_class ?? 'form-control datepicker') !!}
    @endif
</div>
