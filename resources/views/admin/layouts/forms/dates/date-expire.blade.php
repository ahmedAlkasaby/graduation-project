<div class="form-group">
    <label>{{ __('Date Expire') }}</label>
    {!! html()->text('date_expire', old('date_expire'))->class($date_class ?? 'form-control datepicker')->required() !!}
</div>
