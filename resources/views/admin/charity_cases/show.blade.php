@extends('admin.layouts.app')
@section('title') {{ __('Show Shared Case') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => route('admin.charity_cases.index')])
@stop
@section('content')
    @include('admin.errors.alerts')

    {{-- Beneficiary profile --}}
    <div class="card mt-3 mb-4">
        <div class="card-body d-flex flex-column flex-md-row align-items-md-start gap-4">
            <span class="avatar avatar-xl flex-shrink-0">
                <span class="avatar-initial rounded-3 bg-label-primary">
                    <i class="ti ti-user-heart ti-lg"></i>
                </span>
            </span>
            <div class="flex-grow-1">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <h4 class="mb-0">{{ $charityCase->full_name }}</h4>
                    <span class="badge bg-label-info">{{ $charityCase->category?->nameLang($admin_language) }}</span>
                </div>
                <dl class="row mb-0">
                    <dt class="col-sm-3">{{ __('National ID') }}</dt>
                    <dd class="col-sm-9">{{ $charityCase->national_id ?? '—' }}</dd>

                    <dt class="col-sm-3">{{ __('Phone') }}</dt>
                    <dd class="col-sm-9">{{ $charityCase->phone ?? '—' }}</dd>

                    <dt class="col-sm-3">{{ __('Volunteer') }}</dt>
                    <dd class="col-sm-9">{{ $charityCase->volunteer?->full_name ?? __('Unassigned') }}</dd>

                    <dt class="col-sm-3">{{ __('Created At') }}</dt>
                    <dd class="col-sm-9">{{ $charityCase->created_at?->toDateString() }}</dd>
                </dl>
            </div>
            @if ($canEdit)
                <a href="{{ route('admin.charity_cases.edit', $charityCase) }}" class="btn btn-primary waves-effect flex-shrink-0">
                    <i class="ti ti-edit me-1"></i>{{ __('Edit') }}
                </a>
            @endif
        </div>
    </div>

    {{-- The beneficiary's assistances --}}
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0">{{ __('Assistances') }}</h5>
            @if ($canAddAssistance)
                <a href="{{ route('admin.case_assistances.create', ['case_id' => $charityCase->id]) }}" class="btn btn-primary waves-effect">
                    <i class="ti ti-plus me-1"></i>{{ __('Add Assistance') }}
                </a>
            @endif
        </div>
        @if ($assistances->isEmpty())
            <div class="card-body text-center text-muted py-5">
                <i class="ti ti-heart-handshake ti-lg d-block mb-2"></i>
                {{ __('This beneficiary has no assistances yet.') }}
            </div>
        @else
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead class="border-top">
                        <tr>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Installments') }}</th>
                            <th>{{ __('Next Due') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assistances as $assistance)
                            <tr>
                                <td>
                                    @if ($canShowAssistance)
                                        <a href="{{ route('admin.case_assistances.show', $assistance) }}">{{ $assistance->titleLang($admin_language) }}</a>
                                    @else
                                        {{ $assistance->titleLang($admin_language) }}
                                    @endif
                                </td>
                                <td>@include('admin.case_assistances.components.type-badge')</td>
                                <td>@include('admin.case_assistances.components.amount')</td>
                                <td>{{ $assistance->paid_installments_count }} / {{ $assistance->installments_count }} {{ __('paid') }}</td>
                                <td>@include('admin.case_assistances.components.next-due')</td>
                                <td><span class="badge bg-label-{{ $assistance->status()->color() }}">{{ $assistance->status()->label() }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@stop
