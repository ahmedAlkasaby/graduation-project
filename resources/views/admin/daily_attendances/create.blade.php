@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Create DailyAttendance') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.daily_attendances.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.create', ['table' => 'daily_attendances'])
    @include('admin.daily_attendances.components.form', ['action' => 'Add'])
    @include('admin.layouts.forms.close')
@stop

