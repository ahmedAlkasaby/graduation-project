@extends('admin.layouts.app', ['head' => 'headForm','foot' => 'footForm'])
@section('title') {{ __('Edit Role') }}
@stop
@section('head_content')
@include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.roles.index')])
@stop
@section('content')
@include('admin.errors.errors')
@include('admin.layouts.forms.edit',['model'=>$role,'model_id'=>$role->id,'table'=>$role->getTable()])
@include('admin.roles.components.form',['action'=>'Edit'])
@include('admin.layouts.forms.close')
@stop