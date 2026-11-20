@extends('admin.layouts.app')
@section('title') {{ __('Home') }}
@stop
@section('content')
    @include('admin.errors.alerts')

    @foreach ($sections as $section)
        @if ($section['title'])
            <h5 class="mt-6 mb-3">{{ $section['title'] }}</h5>
        @endif
        <div class="row g-4">
            @foreach ($section['cards'] as $card)
                <div class="col-sm-6 col-xl-4">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between align-items-start">
                            <div>
                                <span class="text-heading">{{ $card['label'] }}</span>
                                <h3 class="mb-0 mt-2">
                                    @if ($card['money'] ?? false)
                                        {{ number_format($card['value'], 2) }} <small class="text-muted">{{ $currency }}</small>
                                    @else
                                        {{ number_format($card['value']) }}
                                    @endif
                                </h3>
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
    @endforeach
@stop
