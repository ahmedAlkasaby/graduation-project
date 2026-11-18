@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit TaskAssignee') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.task_assignees.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $task_assignee,
        'model_id' => $task_assignee->id,
        'table' => $task_assignee->getTable(),
    ])
    @include('admin.task_assignees.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop

