@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit RemoteRequest') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.remote_requests.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $remote_request,
        'model_id' => $remote_request->id,
        'table' => $remote_request->getTable(),
    ])
    @include('admin.remote_requests.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop

