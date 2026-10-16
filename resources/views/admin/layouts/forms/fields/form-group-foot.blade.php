@if (count($errors) > 0)
<span class="help-block"><strong id="{{$field_name}}_error">{{$errors->first($field_name)}}</strong></span>
@endif
</div>
