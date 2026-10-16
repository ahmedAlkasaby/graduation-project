<div class="form-group">
    <label>{{ __('Date End') }}</label>
    {!! html()->text('date_end', old('date_end'))->class($date_class ?? 'form-control datepicker')->required() !!}
</div>
