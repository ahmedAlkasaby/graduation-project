@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit TaskComment') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.task_comments.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $task_comment,
        'model_id' => $task_comment->id,
        'table' => $task_comment->getTable(),
    ])
    @include('admin.task_comments.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop

