<div class="form-group">
    <label class="form-label">{{ $name ?? __('Name') }}</label>
    @if(!isset($not_req))
    {!! html()->text($name_text ?? 'name', $name_value ?? null)->class($name_class ?? 'form-control')->required()->attribute('data-parsley-minlength', $minlength ?? '1')->attribute('data-parsley-type', $data_type ?? '') !!}
    @else
    {!! html()->text($name_text ?? 'name', $name_value ?? null)->class($name_class ?? 'form-control')->attribute('data-parsley-minlength', $minlength ?? '1')->attribute('data-parsley-type', $data_type ?? '') !!}
    @endif
</div>
