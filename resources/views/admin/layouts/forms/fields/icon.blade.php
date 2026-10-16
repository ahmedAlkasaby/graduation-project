@php
$field_name = 'icon';
$user_key = 'admin_panel';
if (isset($icon_name)) {
$field_name = $icon_name;
}
if(isset($icon_active) && $icon_active == 0){
$user_key = 'user';
}
@endphp
<input type="file" class="form-control" id="bs-validation-upload-file" required />

<div class="form-group">
    <label>{{ $icon_title ?? __('icon') }}</label><br>
    <input id="{{ $field_name  }}" name="{{ $field_name }}" type="hidden" value="{{ $icon ?? '' }}">
    <img src="{{ $icon ?? '' }}" width="{{ $width ?? 50 }}%" height="auto" @if(!(isset($icon) && ($icon !='' )))style="display:none;" @endif />
    <a href="{{URL::asset("filemanager/dialog.php?type=1&akey=$user_key&field_id=$field_name ")}}" class="btn iframe-btn btn-success ti ti-upload" type="button"></a>
    <a href="#" class="btn btn-danger ti ti-trash  remove_icon_link" type="button"></a>
    <a href="{{ $icon ?? '' }}" class="btn btn-info ti ti-download" type="button" download="" @if (!(isset($icon) && $icon !='' )) style="display:none;" @endif></a>
</div>
