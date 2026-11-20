@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit Shared Case') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => route('admin.charity_cases.show', $charityCase)])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', ['model' => $charityCase, 'table' => 'charity_cases'])
    @include('admin.charity_cases.components.form')
    @include('admin.layouts.forms.close')
@stop
