@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit Task') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => request('from') === 'board' ? route('admin.tasks.board', ['folder' => request('folder_id', isset($task) ? $task->folder_id : null)]) : ($previous_url ?? route('admin.tasks.index'))])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $task,
        'model_id' => $task->id,
        'table' => $task->getTable(),
    ])
    @include('admin.tasks.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop

