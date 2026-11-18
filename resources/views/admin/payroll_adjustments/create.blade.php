@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Create PayrollAdjustment') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.payroll_adjustments.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.create', ['table' => 'payroll_adjustments'])
    @include('admin.payroll_adjustments.components.form', ['action' => 'Add'])
    @include('admin.layouts.forms.close')
@stop

