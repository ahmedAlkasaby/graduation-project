@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit Reminder') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.reminders.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $reminder,
        'model_id' => $reminder->id,
        'table' => $reminder->getTable(),
    ])
    @include('admin.reminders.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop

