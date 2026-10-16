@php
$route_name = getRouteName($route_type ?? NULL);
@endphp
<a class="dropdown-item btn-width {{ $btn_class ?? 'success' }} "
    @if(isset($type_array))
    href="{{ route("$route_name.$table.index",[$type => $type_value ,$type_array => $type_array_value]) }}"
    @else
    href="{{ route("$route_name.$table.index",[$type => $type_value]) }}"
    @endif>
     @if(isset($fa_class))
    <i class="fa-solid  {{ $fa_class }} me-1"></i>
    @else
        <i class="ti ti-{{ $ti_class ?? 'book' }} me-1"></i>
    @endif
    <span> {{ $value ?? __('Orders')}} </span>
</a>
