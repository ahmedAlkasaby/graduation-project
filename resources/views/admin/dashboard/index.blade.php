@extends('admin.layouts.app')
@section('title') {{ __('Home') }}
@stop
@section('content')
    @include('admin.errors.alerts')

    <div class="row g-4">
        @foreach ($cards as $card)
            <div class="col-sm-6 col-xl-4">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-heading">{{ $card['label'] }}</span>
                            <h3 class="mb-0 mt-2">{{ number_format($card['value']) }}</h3>
                        </div>
                        <span class="avatar">
                            <span class="avatar-initial rounded bg-label-{{ $card['color'] }}">
                                <i class="ti ti-{{ $card['icon'] }} ti-md"></i>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@stop
