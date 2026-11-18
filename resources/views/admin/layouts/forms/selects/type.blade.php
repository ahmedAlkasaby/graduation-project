<div class="form-group">
    @if(!isset($label_show))
    <label>{{ __('Type') }}</label>
    @endif
    {!! html()->select('type', postType($all ?? 'no', $is_additions ?? true), null)->class('select2')->required() !!}
</div>
