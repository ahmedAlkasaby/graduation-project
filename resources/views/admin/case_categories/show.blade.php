@extends('admin.layouts.app')
@section('title') {{ __('Show Case Category') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => route('admin.case_categories.index')])
@stop
@section('content')
    <div class="card mt-3">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">{{ __('English Name') }}</dt>
                <dd class="col-sm-9">{{ $caseCategory->nameLang('en') }}</dd>

                <dt class="col-sm-3">{{ __('Arabic Name') }}</dt>
                <dd class="col-sm-9">{{ $caseCategory->nameLang('ar') }}</dd>

                <dt class="col-sm-3">{{ __('Order ID') }}</dt>
                <dd class="col-sm-9">{{ $caseCategory->order_id }}</dd>

                <dt class="col-sm-3">{{ __('Assistances Count') }}</dt>
                <dd class="col-sm-9">{{ $caseCategory->assistances_count }}</dd>

                <dt class="col-sm-3">{{ __('Status') }}</dt>
                <dd class="col-sm-9">{{ statusName((int) $caseCategory->active) }}</dd>
            </dl>
        </div>
    </div>
@stop
