@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit User') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => route('admin.users.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $user,
        'model_id' => $user->id,
        'table' => $user->getTable(),
    ])
    @include('admin.users.components.form')
    @include('admin.layouts.forms.close')
@stop
@section('after_foot')
    @include('admin.users.components.form-js')
@stop
