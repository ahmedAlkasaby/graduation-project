@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Create TaskAttachment') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.task_attachments.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.create', ['table' => 'task_attachments'])
    @include('admin.task_attachments.components.form', ['action' => 'Add'])
    @include('admin.layouts.forms.close')
@stop

