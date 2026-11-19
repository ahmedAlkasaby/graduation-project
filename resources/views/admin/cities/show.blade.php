@extends('admin.layouts.app')
@section('title') {{ __('Show City') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => route('admin.cities.index')])
@stop
@section('content')
    <div class="card mt-3">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">{{ __('English Name') }}</dt>
                <dd class="col-sm-9">{{ $city->nameLang('en') }}</dd>

                <dt class="col-sm-3">{{ __('Arabic Name') }}</dt>
                <dd class="col-sm-9">{{ $city->nameLang('ar') }}</dd>

                <dt class="col-sm-3">{{ __('Country') }}</dt>
                <dd class="col-sm-9">{{ $city->country?->nameLang($admin_language) }}</dd>

                <dt class="col-sm-3">{{ __('Order ID') }}</dt>
                <dd class="col-sm-9">{{ $city->order_id }}</dd>

                <dt class="col-sm-3">{{ __('Latitude') }}</dt>
                <dd class="col-sm-9">{{ $city->latitude }}</dd>

                <dt class="col-sm-3">{{ __('Longitude') }}</dt>
                <dd class="col-sm-9">{{ $city->longitude }}</dd>

                <dt class="col-sm-3">{{ __('Status') }}</dt>
                <dd class="col-sm-9">{{ statusName((int) $city->active) }}</dd>
            </dl>
        </div>
    </div>
@stop
