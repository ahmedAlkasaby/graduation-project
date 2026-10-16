@php
$route_name = getRouteName($route_type ?? NULL);
@endphp
<a id="delete" data-id="{{ $id }}" class=" dropdown-item " href="#"><i class="ti ti-{{ $ti_class ?? 'trash' }} me-1"></i>{{ $deleteName ?? __('Delete') }}</a>
{!! html()->form('DELETE', route("$route_name.$table.destroy", $id))->style('display:inline')->open() !!}
{!! html()->submit('Delete')->class('d-none btn btn-danger delete-btn-submit')->attribute('data-delete-id', $id) !!}
@include('admin.layouts.forms.close')
