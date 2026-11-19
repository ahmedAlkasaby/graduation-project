@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Create Region') }}
@stop
@section('head_content')
@include('admin.layouts.components.head', ['back_url' => route('admin.regions.index')])
@stop
@section('content')
@include('admin.errors.errors')
@include('admin.layouts.forms.create', ['table' => 'regions'])
@include('admin.regions.components.form', ['action' => 'Add'])
@include('admin.layouts.forms.close')
@stop
