@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit AttendanceLog') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.attendance_logs.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $attendance_log,
        'model_id' => $attendance_log->id,
        'table' => $attendance_log->getTable(),
    ])
    @include('admin.attendance_logs.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop

