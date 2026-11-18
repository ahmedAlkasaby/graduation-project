@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit Employee') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.employees.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $employee,
        'model_id' => $employee->id,
        'table' => $employee->getTable(),
    ])
    @include('admin.employees.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop

