@php
$route_name = getRouteName($route_type ?? NULL);
@endphp
<a class="btn-width btn btn-{{ $btn_class ?? 'search' }} fa fa-{{ $fa_class ?? 'primary' }}"
href="{{ route("$route_name.$table.search") }}"><span>{{ $value ?? '' }}</span></a>
