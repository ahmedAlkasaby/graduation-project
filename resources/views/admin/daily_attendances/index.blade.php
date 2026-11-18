@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title')
    {{ __('Daily Attendances') }}
@stop
@section('content')
    @include('admin.daily_attendances.components.head-search')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6 head-label">
                    <h5 class="card-title">{{ __('Daily Attendances') }}</h5>
                </div>
                <div class="col-md-6 dt-action-buttons text-end pt-3 pt-md-0">
                    @include('admin.layouts.components.page-head', [
                        'table' => 'daily_attendances',
                        'create_label' => __('Create DailyAttendance'),
                    ])
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            @include('admin.errors.alerts')
            @include('admin.layouts.table-head')
            @include('admin.daily_attendances.components.table')
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
