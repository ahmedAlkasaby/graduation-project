@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit DailyAttendance') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.daily_attendances.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $daily_attendance,
        'model_id' => $daily_attendance->id,
        'table' => $daily_attendance->getTable(),
    ])
    @include('admin.daily_attendances.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop

