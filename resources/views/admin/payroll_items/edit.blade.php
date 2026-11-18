@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit PayrollItem') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.payroll_items.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $payroll_item,
        'model_id' => $payroll_item->id,
        'table' => $payroll_item->getTable(),
    ])
    @include('admin.payroll_items.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop

