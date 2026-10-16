<div class="form-group">
    <label>{{ __('Address') }}</label>
    {!! html()->text('address', $address ?? null)->class('form-control') !!}
</div>
