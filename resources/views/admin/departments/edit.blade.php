@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit Department') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.departments.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model'    => $department,
        'model_id' => $department->id,
        'table'    => $department->getTable(),
    ])
    @include('admin.departments.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop
