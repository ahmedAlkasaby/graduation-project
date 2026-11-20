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
                <span class="avatar-initial rounded-3 bg-label-primary">
                    <i class="ti ti-heart-handshake ti-lg"></i>
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

    {{-- Stats --}}
    <div class="row g-4 mb-4">
        @foreach ([
            ['label' => __('Installments'), 'value' => $stats['total'], 'icon' => 'calendar', 'color' => 'primary'],
            ['label' => __('Paid'), 'value' => $stats['paid'], 'icon' => 'circle-check', 'color' => 'success'],
            ['label' => __('Pending'), 'value' => $stats['pending'], 'icon' => 'clock', 'color' => 'warning'],
            ['label' => __('Overdue'), 'value' => $stats['overdue'], 'icon' => 'alert-triangle', 'color' => 'danger'],
            ['label' => __('Planned Total'), 'value' => number_format((float) $stats['planned_amount'], 2) . ' ' . $currency, 'icon' => 'cash', 'color' => 'info'],
        ] as $stat)
            <div class="col-sm-6 col-xl">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-heading">{{ $stat['label'] }}</span>
                            <h4 class="mb-0 mt-2">{{ $stat['value'] }}</h4>
                        </div>
                        <span class="avatar">
                            <span class="avatar-initial rounded bg-label-{{ $stat['color'] }}">
                                <i class="ti ti-{{ $stat['icon'] }} ti-md"></i>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        {{-- Assistance --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Assistance') }}</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">{{ __('Schedule') }}</dt>
                        <dd class="col-sm-8">
                            {{ $stats['first_due_at']?->toDateString() }}
                            @if ($assistance->isRecurring())
                                → {{ $stats['last_due_at']?->toDateString() }}
                            @endif
                        </dd>

                        <dt class="col-sm-4">{{ __('Description') }}</dt>
                        <dd class="col-sm-8">{{ $assistance->description ?: '—' }}</dd>

                        <dt class="col-sm-4">{{ __('Created By') }}</dt>
                        <dd class="col-sm-8">{{ $assistance->creator?->full_name }}</dd>

                        <dt class="col-sm-4">{{ __('Created At') }}</dt>
                        <dd class="col-sm-8 mb-0">{{ $assistance->created_at?->toDateString() }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Beneficiary --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Beneficiary') }}</h5>
                    @if ($canShowCase)
                        <a href="{{ route('admin.charity_cases.show', $assistance->charityCase) }}" class="btn btn-sm btn-label-primary">{{ __('Open Profile') }}</a>
                    @endif
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">{{ __('Full Name') }}</dt>
                        <dd class="col-sm-8">{{ $assistance->charityCase?->full_name }}</dd>

                        <dt class="col-sm-4">{{ __('National ID') }}</dt>
                        <dd class="col-sm-8">{{ $assistance->charityCase?->national_id ?? '—' }}</dd>

                        <dt class="col-sm-4">{{ __('Phone') }}</dt>
                        <dd class="col-sm-8">{{ $assistance->charityCase?->phone ?? '—' }}</dd>

                        <dt class="col-sm-4">{{ __('Category') }}</dt>
                        <dd class="col-sm-8">{{ $assistance->charityCase?->category?->nameLang($admin_language) }}</dd>

                        <dt class="col-sm-4">{{ __('Volunteer') }}</dt>
                        <dd class="col-sm-8 mb-0">{{ $assistance->charityCase?->volunteer?->full_name ?? __('Unassigned') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    {{-- Installments --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Installments') }}</h5>
            <small class="text-muted">{{ __('Payments are recorded by the payments module, not here.') }}</small>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead class="border-top">
                    <tr>
                        <th>#</th>
                        <th>{{ __('Due Date') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Paid At') }}</th>
                        <th>{{ __('Cancelled At') }}</th>
                        <th>{{ __('Notes') }}</th>
                        @if ($canCancelInstallment)
                            <th>{{ __('Setting') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($installments as $installment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $installment->due_at->toDateString() }}</td>
                            <td>{{ number_format((float) $installment->amount, 2) }} {{ $currency }}</td>
                            <td><span class="badge bg-label-{{ $installment->status()->color() }}">{{ $installment->status()->label() }}</span></td>
                            <td>{{ $installment->paid_at?->toDateTimeString() ?? '—' }}</td>
                            <td>{{ $installment->cancelled_at?->toDateTimeString() ?? '—' }}</td>
                            <td class="text-wrap">{{ $installment->notes ?? '—' }}</td>
                            @if ($canCancelInstallment)
                                <td>
                                    @if ($installment->isPending())
                                        <button type="button" class="btn btn-sm btn-label-danger waves-effect"
                                            data-bs-toggle="modal" data-bs-target="#cancel-installment-modal"
                                            data-cancel-url="{{ route('admin.case_assistances.cancel_installment', [$assistance, $installment]) }}"
                                            data-cancel-label="{{ $installment->due_at->toDateString() }}">
                                            {{ __('Cancel') }}
                                        </button>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
@section('after_foot')
    @if ($canCancelInstallment)
        @include('admin.case_assistances.components.cancel-installment-modal')
    @endif
@stop
