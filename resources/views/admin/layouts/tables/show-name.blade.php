@php
$route_name = getRouteName($route_type ?? NULL);
@endphp
<a class="table-name-link {{ $link_class ?? '' }} " href="{{ route("$route_name.$table.show",$id) }}"><span> {!! $edit ?? '' !!} </span></a>

