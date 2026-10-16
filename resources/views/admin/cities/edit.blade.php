@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit City') }}
@stop
@section('head_content')
@include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.cities.index')])
@stop
@section('content')
@include('admin.errors.errors')
@include('admin.layouts.forms.edit', ['model' => $city, 'model_id' => $city->id, 'table' => $city->getTable()])
@include('admin.cities.components.form', ['action' => 'Edit'])
@include('admin.layouts.forms.close')
@stop
