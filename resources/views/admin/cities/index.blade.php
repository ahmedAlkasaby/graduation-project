@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Cities') }}
@stop
@section('head_content')
@stop
@section('content')
    @include('admin.cities.components.head-search')

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6 head-label">
                    <h5 class="card-title">{{ __('Cities') }}</h5>
                </div>
                <div class="col-md-6 dt-action-buttons text-end pt-3 pt-md-0">
                    @include('admin.layouts.components.page-head', ['table' => 'cities', 'create_label' => __('Create City')])
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            @include('admin.errors.alerts')
            @include('admin.layouts.table-head')
            @include('admin.cities.components.table')
            @include('admin.layouts.table-foot')
            @include('admin.layouts.paginate-all')
        </div>
    </div>
@stop
@section('after_foot')
    @include('admin.layouts.filter')
@include('admin.layouts.status')
@include('admin.layouts.delete')
@stop
