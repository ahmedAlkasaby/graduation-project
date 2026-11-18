@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit ApprovalStep') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.approval_steps.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $approval_step,
        'model_id' => $approval_step->id,
        'table' => $approval_step->getTable(),
    ])
    @include('admin.approval_steps.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop

