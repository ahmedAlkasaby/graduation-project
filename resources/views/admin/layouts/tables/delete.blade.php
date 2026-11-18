@php
$route_name = getRouteName($route_type ?? NULL);
@endphp
<a id="delete" data-id="{{ $id }}"><i class="ti ti-{{ $ti_class ?? 'trash' }} me-1">{{ $deleteName ?? __('Delete') }}</i></a>
<a data-delete-id="{{ $id }}" class="dropdown-item {{ $serivce_model ?? '' }}-delete  d-none btn btn-danger delete-btn-submit"></a>
