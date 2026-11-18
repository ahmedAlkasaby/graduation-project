@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit TaskAttachment') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.task_attachments.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $task_attachment,
        'model_id' => $task_attachment->id,
        'table' => $task_attachment->getTable(),
    ])
    @include('admin.task_attachments.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop

