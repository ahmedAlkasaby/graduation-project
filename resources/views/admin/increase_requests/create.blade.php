@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Create IncreaseRequest') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.increase_requests.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.create', ['table' => 'increase_requests'])
    @include('admin.increase_requests.components.form', ['action' => 'Add'])
    @include('admin.layouts.forms.close')
@stop

