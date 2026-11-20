@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Create Shared Case') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', [
        'back_url' => $returnTo ? route('admin.case_assistances.create') : route('admin.charity_cases.index'),
    ])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.create', ['table' => 'charity_cases'])
    @include('admin.charity_cases.components.form')
    @include('admin.layouts.forms.close')
@stop
