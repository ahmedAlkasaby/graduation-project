@php
$route_name = getRouteName($route_type ?? NULL);
@endphp
<a class="btn-width btn btn-{{ $btn_class ?? 'primary' }} fa fa-{{ $fa_class ?? 'edit' }}" href="{{ route("$route_name.$table.edit",[$first_model => $first_model_id, $second_model => $second_model_id]) }}"><span> {{ $edit ?? '' }} </span></a>
