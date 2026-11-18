@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Create ApprovalStep') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.approval_steps.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.create', ['table' => 'approval_steps'])
    @include('admin.approval_steps.components.form', ['action' => 'Add'])
    @include('admin.layouts.forms.close')
@stop

