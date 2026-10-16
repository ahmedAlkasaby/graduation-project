@php
$route_name = getRouteName($route_type ?? NULL);
@endphp
<a class="btn-width btn btn-{{ $btn_class ?? 'paint-brush' }} fa fa-{{ $fa_class ?? 'info' }}"
href="{{ route("$route_name.$table.index") }}"><span>{{ $value ?? '' }}</span></a>
