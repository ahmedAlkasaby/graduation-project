@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit PayrollAdjustment') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.payroll_adjustments.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $payroll_adjustment,
        'model_id' => $payroll_adjustment->id,
        'table' => $payroll_adjustment->getTable(),
    ])
    @include('admin.payroll_adjustments.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop

