@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Create Assistance') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => route('admin.case_assistances.index')])
@stop
@section('content')
    @include('admin.errors.errors')
    @include('admin.layouts.forms.create', ['table' => 'case_assistances', 'form_validate' => 'no', 'form_class' => 'assistance-form'])
    @include('admin.case_assistances.components.form')
    @include('admin.layouts.forms.close')
@stop
@section('after_foot')
    @if ($canQuickCreateCase)
        @include('admin.case_assistances.components.quick-case-modal')
    @endif
    @include('admin.case_assistances.components.form-js')
@stop
