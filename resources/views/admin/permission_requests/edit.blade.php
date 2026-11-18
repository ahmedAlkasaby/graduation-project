@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit PermissionRequest') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.permission_requests.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $permission_request,
        'model_id' => $permission_request->id,
        'table' => $permission_request->getTable(),
    ])
    @include('admin.permission_requests.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop

