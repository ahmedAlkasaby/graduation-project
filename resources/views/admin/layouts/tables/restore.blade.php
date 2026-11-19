@php
    $route_name = getRouteName($route_type ?? null);
@endphp
<a id="restore" data-id="{{ $id }}" class="dropdown-item" href="#"><i class="ti ti-{{ $ti_class ?? 'restore' }} me-1"></i>{{ $restoreName ?? __('Restore') }}</a>
{!! html()->form('PATCH', route("$route_name.$table.restore", $id))->style('display:inline')->open() !!}
{!! html()->submit('Restore')->class('d-none btn btn-danger restore-btn-submit')->attribute('data-restore-id', $id) !!}
@include('admin.layouts.forms.close')
