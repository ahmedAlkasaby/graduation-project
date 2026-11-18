@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Create Employee') }}
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.create', ['table' => 'employees'])
    @include('admin.employees.components.form', ['action' => 'Add'])
    @include('admin.layouts.forms.close')
@stop

