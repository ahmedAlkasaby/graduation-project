@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Create TaskTimeEntry') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.task_time_entries.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.create', ['table' => 'task_time_entries'])
    @include('admin.task_time_entries.components.form', ['action' => 'Add'])
    @include('admin.layouts.forms.close')
@stop

