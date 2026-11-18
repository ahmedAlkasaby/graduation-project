@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit Reason') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.reasons.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $reason,
        'model_id' => $reason->id,
        'table' => $reason->getTable(),
    ])
    @include('admin.reasons.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop

