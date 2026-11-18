<div class="form-group">
    <label>{{ __('Date Start') }}</label>
    {!! html()->text('date_start', old('date_start'))->class($date_class ?? 'form-control datepicker')->required() !!}
</div>
