<div class="form-group">
    @if(!isset($label_show))
    <label>{{ __('Country') }}</label>
    @endif
    {!! html()->select('country_id', $countries, $country_id ?? null)->class('select2')->required()->attribute('style', 'width: 100%') !!}
</div>
