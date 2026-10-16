@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit User') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.users.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model' => $user,
        'model_id' => $user->id,
        'table' => $user->getTable(),
        'enctype' => 'multipart/form-data',
    ])
    @include('admin.users.components.form')
    @include('admin.layouts.forms.close')

@stop
@section('after_foot')
    {{-- @include('admin.layouts.image') --}}

    @include('admin.users.components.repeater')
    @include('admin.users.components.change-type')
@stop
