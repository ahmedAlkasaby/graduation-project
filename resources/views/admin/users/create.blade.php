@extends('admin.layouts.app', ['head' => 'headForm','foot' => 'footForm'])
@section('title') {{ __('Create User') }}
@stop
@section('head_content')
@include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.users.index')])
@stop
@section('content')
@include('admin.errors.errors')
@include('admin.layouts.forms.create',['table'=>'users','enctype' => 'multipart/form-data'])
@include('admin.users.components.form')
@include('admin.layouts.forms.close')

@stop
@section('after_foot')
{{-- @include('admin.layouts.image') --}}

@include('admin.users.components.change-type')
@include('admin.users.components.repeater')
@stop
