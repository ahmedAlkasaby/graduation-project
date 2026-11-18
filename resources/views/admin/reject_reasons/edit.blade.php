@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit RejectReason') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.reject_reasons.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $reject_reason,
        'model_id' => $reject_reason->id,
        'table' => $reject_reason->getTable(),
    ])
    @include('admin.reject_reasons.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop

