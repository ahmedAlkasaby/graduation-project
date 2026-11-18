@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title')
    {{ __('Payrolls') }}
@stop
@section('content')
    @include('admin.payrolls.components.head-search')
    @include('admin.payrolls.components.generate')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6 head-label">
                    <h5 class="card-title">{{ __('Payrolls') }}</h5>
                </div>
                <div class="col-md-6 dt-action-buttons text-end pt-3 pt-md-0">
                    <a href="{{ route('admin.payrolls.report') }}" class="btn btn-outline-secondary me-2">
                        <i class="ti ti-report-money me-1"></i>{{ __('Reports') }}
                    </a>
                    @include('admin.layouts.components.page-head', [
                        'table' => 'payrolls',
                        'create_label' => __('Create Payroll'),
                    ])
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            @include('admin.errors.alerts')
            @include('admin.layouts.table-head')
            @include('admin.payrolls.components.table')
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
