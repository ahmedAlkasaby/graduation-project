@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Create PayrollItem') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.payroll_items.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.create', ['table' => 'payroll_items'])
    @include('admin.payroll_items.components.form', ['action' => 'Add'])
    @include('admin.layouts.forms.close')
@stop

