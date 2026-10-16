<div class="form-group">
    @if(!isset($label_show))
    <label>{{ __('City') }}</label>
    @endif
    {!! html()->select('city_id', $cities, $city_id ?? null)->class('select2')->required()->attribute('style', 'width: 100%') !!}
</div>
