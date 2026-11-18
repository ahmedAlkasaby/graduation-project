@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Create Task') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => request('from') === 'board' ? route('admin.tasks.board', ['folder' => request('folder_id', isset($task) ? $task->folder_id : null)]) : ($previous_url ?? route('admin.tasks.index'))])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.create', ['table' => 'tasks'])
    @include('admin.tasks.components.form', ['action' => 'Add'])
    @include('admin.layouts.forms.close')
@stop

