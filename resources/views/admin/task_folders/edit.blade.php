@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit TaskFolder') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.task_folders.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $task_folder,
        'model_id' => $task_folder->id,
        'table' => $task_folder->getTable(),
    ])
    @include('admin.task_folders.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop

