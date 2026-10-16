@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footFormEditor'])
@section('title')
    @if (isset($title))
        {{ $title }}
    @else
        {{ __('Users') }}
    @endif
@stop
@section('content')
    @include('admin.users.components.head-search')
    <!-- User List Table -->
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6 head-label">
                    @if (isset($title))
                        <h5 class="card-title">{{ __('Users') }} - {{ $title }}</h5>
                    @endif
                </div>
                <div class="col-md-6 dt-action-buttons text-end pt-3 pt-md-0">
                    @include('admin.layouts.components.page-head', ['table' => 'users', 'create_label' => __('Create User')])
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            @include('admin.errors.alerts')
            @include('admin.layouts.table-head')
            @include('admin.users.components.table')
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
