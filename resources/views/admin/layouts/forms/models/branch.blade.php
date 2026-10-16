<div class="form-group">
    @if(!isset($label_show))
    <label>{{ __('Branch') }}</label>
    @endif
    {!! html()->select('branch_id', $branches, $branch_id ?? null)->class('select2')->required()->attribute('style', 'width: 100%') !!}
</div>
