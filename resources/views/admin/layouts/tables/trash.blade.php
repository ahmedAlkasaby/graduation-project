@php
$route_name = getRouteName($route_type ?? NULL);
@endphp
<a class="btn-width btn btn-{{ $btn_class ?? 'danger' }} fa fa-{{ $fa_class ?? 'trash' }}"
@if(isset($type) && $type != "")
href="{{ route("$route_name.$table.trash",['type'=>$type]) }}"
@else
href="{{ route("$route_name.$table.trash") }}"
@endif
><span> {{ $value ?? __("Recycle Bin") }} </span></a>
