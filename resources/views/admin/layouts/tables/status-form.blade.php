@php
$route_name = getRouteName($route_type ?? NULL);
@endphp
{!! html()->form('POST', route("$route_name.$table.status", $id))->style('display:inline')->open() !!}
<button type="submit" class="fa fa-{{ $success_fa ?? 'check' }} btn-width btn btn-{{ $btn_class ?? 'success' }}"></button>
@include('admin.layouts.forms.close')

