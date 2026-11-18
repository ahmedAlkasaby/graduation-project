@php
$field_name = 'attachment';
$user_key = 'admin_panel';
if (isset($attachment_name)) {
    $field_name = $attachment_name;
}
if(isset($attachment_active) && $attachment_active == 0){
    $user_key = 'user';
}
@endphp
<div class="form-group">
    <label>{{ $attachment_title ?? __('Attachment') }}</label>
    <input id="{{ $field_name }}" name="{{ $field_name }}" type="hidden" value="{{ $attachment ?? '' }}">
    <a href="{{ $attachment ?? '' }}" class="btn iframe-btn btn-info fa fa-download" type="button"@if( !(isset($attachment) && ($attachment != '')))style="display:none;"@endif></a>
    <a href="{{URL::asset("filemanager/dialog.php?type=2&akey=$user_key&field_id=$field_name")}}" class="btn iframe-btn btn-success fa fa-upload" type="button"></a>
    <a href="#" class="btn btn-danger fa fa-remove  remove_attachment_link" type="button"></a>
</div>

