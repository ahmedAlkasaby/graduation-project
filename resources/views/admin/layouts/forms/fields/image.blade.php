@php
$field_name = 'image';
$user_key = 'admin_panel';
if (isset($image_name)) {
    $field_name = $image_name;
}
if(isset($image_active) && $image_active == 0){
    $user_key = 'user';
}
@endphp
<input type="file" class="form-control" id="bs-validation-upload-file" required />

<div class="form-group">
    <label>{{ $image_title ?? __('Image') }}</label><br>
    <input id="{{ $field_name  }}" name="{{ $field_name }}" type="hidden" value="{{ $image ?? '' }}">
    <img  src="{{ $image ?? '' }}"  width="{{ $width ?? 50 }}%" height="auto"@if(!(isset($image) && ($image != '')))style="display:none;"@endif/>
    <a href="{{URL::asset("filemanager/dialog.php?type=1&akey=$user_key&field_id=$field_name ")}}" class="btn iframe-btn btn-success ti ti-upload" type="button"></a>
    <a href="#" class="btn btn-danger ti ti-remove  remove_image_link" type="button"></a>
    <a href="{{ $image ?? '' }}" class="btn btn-info ti ti-download" type="button" download="" @if (!(isset($image) && $image != '')) style="display:none;" @endif></a>
</div>