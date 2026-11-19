@extends('admin.layouts.app')
@section('title') {{ __('Show Region') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => route('admin.regions.index')])
@stop
@section('content')
    <div class="card mt-3">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">{{ __('English Name') }}</dt>
                <dd class="col-sm-9">{{ $region->nameLang('en') }}</dd>

                <dt class="col-sm-3">{{ __('Arabic Name') }}</dt>
                <dd class="col-sm-9">{{ $region->nameLang('ar') }}</dd>

                <dt class="col-sm-3">{{ __('City') }}</dt>
                <dd class="col-sm-9">{{ $region->city?->nameLang($admin_language) }}</dd>

                <dt class="col-sm-3">{{ __('Order ID') }}</dt>
                <dd class="col-sm-9">{{ $region->order_id }}</dd>

                <dt class="col-sm-3">{{ __('Latitude') }}</dt>
                <dd class="col-sm-9">{{ $region->latitude }}</dd>

                <dt class="col-sm-3">{{ __('Longitude') }}</dt>
                <dd class="col-sm-9">{{ $region->longitude }}</dd>

                <dt class="col-sm-3">{{ __('Status') }}</dt>
                <dd class="col-sm-9">{{ statusName((int) $region->active) }}</dd>
            </dl>
        </div>
    </div>
@stop
