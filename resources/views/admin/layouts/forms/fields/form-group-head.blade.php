@if (count($errors) > 0)
    <div class="form-group {{ $errors->has($field_name) ? 'has-error' : '' }}" id="{{ $field_name }}_wrap">
@else
    <div class="form-group" id="{{ $field_name }}_wrap">
@endif
