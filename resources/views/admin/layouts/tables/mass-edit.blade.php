<td>
    @if (isset($class) && $class == $class_name)
    <input class="listing_checkbox" type="checkbox" name="id[]" value="{{ $model->id }}" />
@else
    {!! $key + 1 !!}
@endif
</td>
