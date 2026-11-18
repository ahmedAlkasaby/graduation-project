<th>
@if (isset($class) && $class == $class_name)
    <input id="main_listing_checkbox" type="checkbox" value="" />
@else
   {{ __('#') }}
@endif
</th>
