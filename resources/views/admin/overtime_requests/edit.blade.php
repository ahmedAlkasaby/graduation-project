@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit OvertimeRequest') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.overtime_requests.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $overtime_request,
        'model_id' => $overtime_request->id,
        'table' => $overtime_request->getTable(),
    ])
    @include('admin.overtime_requests.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop

