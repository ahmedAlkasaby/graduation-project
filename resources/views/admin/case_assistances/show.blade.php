@extends('admin.layouts.app')
@section('title') {{ __('Show Assistance') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => route('admin.case_assistances.index')])
@stop
@section('content')
    @include('admin.errors.errors')

    {{-- Header --}}
    <div class="card mt-3 mb-4">
        <div class="card-body d-flex flex-column flex-md-row align-items-md-center gap-4">
            <span class="avatar avatar-xl flex-shrink-0">
                <span class="avatar-initial rounded-3 bg-label-{{ $assistance->isRecurring() ? 'primary' : 'info' }}">
                    <i class="ti ti-{{ $assistance->isRecurring() ? 'calendar-repeat' : 'gift' }} ti-lg"></i>
                </span>
            </span>
            <div class="flex-grow-1">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <h4 class="mb-0">{{ $assistance->titleLang($admin_language) }}</h4>
                    @include('admin.case_assistances.components.type-badge')
                    <span class="badge bg-label-{{ $assistance->status()->color() }}">{{ $assistance->status()->label() }}</span>
                </div>
                <div class="fs-5">@include('admin.case_assistances.components.amount')</div>
            </div>
            @if ($canEdit)
                <a href="{{ route('admin.case_assistances.edit', $assistance) }}" class="btn btn-primary waves-effect flex-shrink-0">
                    <i class="ti ti-edit me-1"></i>{{ __('Edit') }}
                </a>
            @endif
        </div>
    </div>

    @if ($assistance->isRecurring())
        @include('admin.case_assistances.components.show.recurring')
    @else
        @include('admin.case_assistances.components.show.one-time')
    @endif
@stop
@section('after_foot')
    @if ($canCancelInstallment)
        @include('admin.case_assistances.components.cancel-installment-modal')
    @endif
@stop
