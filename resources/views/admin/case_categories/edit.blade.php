@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit Case Category') }}
@stop
@section('head_content')
@include('admin.layouts.components.head', ['back_url' => route('admin.case_categories.index')])
@stop
@section('content')
@include('admin.errors.errors')
@include('admin.layouts.forms.edit', ['model' => $caseCategory, 'model_id' => $caseCategory->id, 'table' => $caseCategory->getTable()])
@include('admin.case_categories.components.form', ['action' => 'Edit'])
@include('admin.layouts.forms.close')
@stop
