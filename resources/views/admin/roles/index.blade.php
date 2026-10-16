@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Roles') }}
@stop
@section('head_content')
@stop
@section('content')
    @include('admin.roles.components.head-search')

              <!-- Basic Bootstrap Table -->
              <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6 head-label"><h5 class="card-title">{{__("Roles")}}</h5></div>
                        <div class="col-md-6 dt-action-buttons text-end pt-3 pt-md-0">
                            @include('admin.layouts.components.page-head', ['table' => 'roles', 'create_label' => __('Create Role')])
                        </div>
                   </div> 
                </div>
                <div class="table-responsive text-nowrap">
                    @include('admin.errors.alerts')
                    @include('admin.layouts.table-head')
                    @include('admin.roles.components.table')
                    @include('admin.layouts.table-foot')
                    @include('admin.layouts.paginate-all')
                </div>
              </div>
              <!--/ Basic Bootstrap Table -->
@stop
@section('after_foot')
    @include('admin.layouts.filter')
@include('admin.layouts.delete')
@stop


