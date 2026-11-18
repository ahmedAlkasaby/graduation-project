@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit Job Title') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.job_titles.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.edit', [
        'model'    => $job_title,
        'model_id' => $job_title->id,
        'table'    => $job_title->getTable(),
    ])
    @include('admin.job_titles.components.form', ['action' => 'Edit'])
    @include('admin.layouts.forms.close')
@stop
