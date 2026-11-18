<div class="form-group">
    @if(!isset($label_show))
    <label>{{ __('Unit') }}</label>
    @endif
    {!! html()->select('unit_id', $units, $unit_id ?? null)->class('select2')->required()->attribute('style', 'width: 100%') !!}
</div>
