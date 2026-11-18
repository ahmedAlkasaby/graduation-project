{{--  @if(!isset($model_restore))
<a data-toggle="tooltip"  id="restore" data-id="{{ $id }}" class="btn-width btn btn-{{ $restore_class ?? 'info' }} fa fa-{{ $restore_fa_class ?? 'undo' }}"><span>{{ $restore ?? '' }}</span></a>
{!! Form::open(['method' => 'DELETE','route' => ["$route_name.$table.restore", $id],'style'=>'display:inline']) !!}
{!! Form::submit('Delete', ['class' => 'hide btn btn-danger restore-btn-submit','data-restore-id' => $id]) !!}
@include('admin.layouts.forms.close')
@endif  --}}
@php
$route_name = getRouteName($route_type ?? NULL);
@endphp
<a id="restore" data-id="{{ $id }}" class=" dropdown-item " href="#"><i class="ti ti-{{ $ti_class ?? 'trash' }} me-1"></i>{{ $restoreName ?? __('Restore')}}</a>
{!! html()->form('DELETE', route("$route_name.$table.restore", $id))->style('display:inline')->open() !!}
{!! html()->submit('Restore')->class('d-none btn btn-danger restore-btn-submit')->attribute('data-restore-id', $id) !!}
@include('admin.layouts.forms.close')
