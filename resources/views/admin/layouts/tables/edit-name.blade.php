@php
$route_name = getRouteName($route_type ?? NULL);
@endphp
<a class=" {{ $link_class ?? '' }} " href="{{ route("$route_name.$table.edit",$id) }}"><span> {{ $edit ?? '' }} </span></a>
{{--  table-name-link  --}}
