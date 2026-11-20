@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Create Case Category') }}
@stop
@section('head_content')
@include('admin.layouts.components.head', ['back_url' => route('admin.case_categories.index')])
@stop
@section('content')
@include('admin.errors.errors')
@include('admin.layouts.forms.create', ['table' => 'case_categories'])
@include('admin.case_categories.components.form', ['action' => 'Add'])
@include('admin.layouts.forms.close')
@stop
