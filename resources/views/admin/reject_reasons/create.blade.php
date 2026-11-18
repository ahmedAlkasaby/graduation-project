@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Create RejectReason') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.reject_reasons.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.create', ['table' => 'reject_reasons'])
    @include('admin.reject_reasons.components.form', ['action' => 'Add'])
    @include('admin.layouts.forms.close')
@stop

