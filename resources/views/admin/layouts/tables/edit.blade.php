@php
    $route_name = getRouteName($route_type ?? null);
@endphp
@if (isset($model_id))
    @if (isset($model_type))
        <a class="dropdown-item" href="{{ route("$route_name.$table.edit", [$id, $model_id]) }}">
            <i class="ti ti-{{ $ti_class ?? 'pencil' }} me-1"></i> {{ $editName ?? __('Edit') }}</a>
    @else
        <a class="dropdown-item" href="{{ route("$route_name.$table.edit", [$model_id, $id]) }}">
            <i class="ti ti-{{ $ti_class ?? 'pencil' }} me-1"></i> {{ $editName ?? __('Edit') }}</a>
    @endif
@else
    <a class="dropdown-item" href="{{ route("$route_name.$table.edit", $id) }}">
            <i class="ti ti-{{ $ti_class ?? 'pencil' }} me-1"></i> {{ $editName ?? __('Edit') }}</a>
@endif
