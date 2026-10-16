@extends('admin.layouts.app',['head' => 'headForm','foot' => 'footForm'])
@section('title') {{ __('Create Role') }}
@stop
@section('head_content')
@include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.roles.index')])
@stop
@section('content')
@include('admin.errors.errors')
@include('admin.layouts.forms.create',['table'=>'roles'])
@include('admin.roles.components.form',['action'=>'Add'])
@include('admin.layouts.forms.close')
@stop
@section('after_foot')
@include('admin.layouts.image')
@include('admin.layouts.tinymce')
@stop