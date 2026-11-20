@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Shared Cases') }}
@stop
@section('head_content')
@stop
@section('content')
    @include('admin.charity_cases.components.head-search')

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6 head-label">
                    <h5 class="card-title">{{ __('Shared Cases') }}</h5>
                </div>
                <div class="col-md-6 dt-action-buttons text-end pt-3 pt-md-0">
                    @include('admin.layouts.components.page-head', ['table' => 'charity_cases', 'create_label' => __('Create Shared Case')])
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            @include('admin.errors.alerts')
            @include('admin.layouts.table-head')
            @include('admin.charity_cases.components.table')
            @include('admin.layouts.table-foot')
        </div>
        @include('admin.layouts.paginate-all')
    </div>
@stop
@section('after_foot')
    @include('admin.layouts.filter')
@stop
