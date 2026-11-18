@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit AttendanceCorrection') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.attendance_corrections.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $attendance_correction,
        'model_id' => $attendance_correction->id,
        'table' => $attendance_correction->getTable(),
    ])
    @include('admin.attendance_corrections.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop

