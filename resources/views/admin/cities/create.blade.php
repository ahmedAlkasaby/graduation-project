@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Create City') }}
@stop
@section('head_content')
@include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.cities.index')])
@stop
@section('content')
@include('admin.errors.errors')
@include('admin.layouts.forms.create', ['table' => 'cities'])
@include('admin.cities.components.form', ['action' => 'Add'])
@include('admin.layouts.forms.close')
@stop
