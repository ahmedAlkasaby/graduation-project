@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Create AttendanceCorrection') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.attendance_corrections.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.create', ['table' => 'attendance_corrections'])
    @include('admin.attendance_corrections.components.form', ['action' => 'Add'])
    @include('admin.layouts.forms.close')
@stop

