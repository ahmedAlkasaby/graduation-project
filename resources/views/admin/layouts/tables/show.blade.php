@php
$route_name = getRouteName($route_type ?? NULL);
@endphp
@if(!isset($model_show))
<a class="dropdown-item" href="{{ route("$route_name.$table.show",$id) }}"><i class="ti ti-{{ $ti_class ?? 'eye' }} me-1"></i>  {{ $showName ??__('Show') }}</a>
@elseif(isset($route_all))
<a class="dropdown-item" href="{{ route("$route_name.$route_all",$id) }}"> <i class="ti ti-{{ $ti_class ?? 'eye' }} me-1"></i> {{ $showName ?? __('Show') }}</a>
@endif
