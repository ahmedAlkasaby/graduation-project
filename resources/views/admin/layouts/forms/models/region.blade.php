<div class="form-group">
    @if(!isset($label_show))
    <label>{{ __('Region') }}</label>
    @endif
    {!! html()->select('region_id', $regions, $region_id ?? null)->class('select2')->required()->attribute('style', 'width: 100%') !!}
</div>
