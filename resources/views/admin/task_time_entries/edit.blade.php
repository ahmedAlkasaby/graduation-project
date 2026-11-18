@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit TaskTimeEntry') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.task_time_entries.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $task_time_entry,
        'model_id' => $task_time_entry->id,
        'table' => $task_time_entry->getTable(),
    ])
    @include('admin.task_time_entries.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop

