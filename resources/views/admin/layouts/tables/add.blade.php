@php
    $route_name = getRouteName($route_type ?? null);
    $create_url = isset($create_type) && isset($type_id)
        ? route("$route_name.$table.create", [$create_type => $type_id])
        : route("$route_name.$table.create", $param ?? null);
@endphp

<a class="btn btn-primary waves-effect waves-light btn-{{ $btn_class ?? 'success' }}" href="{{ $create_url }}">
    <i class="ti ti-plus me-sm-1"></i><span>{{ $value ?? '' }}</span>
</a>
