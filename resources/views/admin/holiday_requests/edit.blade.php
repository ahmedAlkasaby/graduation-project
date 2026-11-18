@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit HolidayRequest') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.holiday_requests.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $holiday_request,
        'model_id' => $holiday_request->id,
        'table' => $holiday_request->getTable(),
    ])
    @include('admin.holiday_requests.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop

