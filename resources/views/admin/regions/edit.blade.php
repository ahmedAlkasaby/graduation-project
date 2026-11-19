@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit Region') }}
@stop
@section('head_content')
@include('admin.layouts.components.head', ['back_url' => route('admin.regions.index')])
@stop
@section('content')
@include('admin.errors.errors')
@include('admin.layouts.forms.edit', ['model' => $region, 'model_id' => $region->id, 'table' => $region->getTable()])
@include('admin.regions.components.form', ['action' => 'Edit'])
@include('admin.layouts.forms.close')
@stop
