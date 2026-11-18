@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit IncreaseRequest') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.increase_requests.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $increase_request,
        'model_id' => $increase_request->id,
        'table' => $increase_request->getTable(),
    ])
    @include('admin.increase_requests.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop

