{{--
    A monthly assistance is a schedule: counters, how far along it is, then
    every installment with its own state.
--}}

{{-- Counters --}}
<div class="row g-4 mb-4">
    @foreach ([
        ['label' => __('Installments'), 'value' => $stats['total'], 'icon' => 'calendar', 'color' => 'primary'],
        ['label' => __('Paid'), 'value' => $stats['paid'], 'icon' => 'circle-check', 'color' => 'success'],
        ['label' => __('Pending'), 'value' => $stats['pending'], 'icon' => 'clock', 'color' => 'warning'],
        ['label' => __('Overdue'), 'value' => $stats['overdue'], 'icon' => 'alert-triangle', 'color' => 'danger'],
        ['label' => __('Cancelled'), 'value' => $stats['cancelled'], 'icon' => 'ban', 'color' => 'secondary'],
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
    {{-- Schedule progress --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('Schedule') }}</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-medium">{{ $stats['paid'] }} / {{ $stats['total'] }} {{ __('paid') }}</span>
                    <span class="text-muted">{{ $stats['paid_percent'] }}%</span>
                </div>
                <div class="progress mb-4" style="height: 8px">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $stats['paid_percent'] }}%"
                        aria-valuenow="{{ $stats['paid_percent'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <dl class="row mb-0">
                    <dt class="col-6">{{ __('First Installment') }}</dt>
                    <dd class="col-6">{{ $stats['first_due_at']?->toDateString() }}</dd>

                    <dt class="col-6">{{ __('Last Installment') }}</dt>
                    <dd class="col-6">{{ $stats['last_due_at']?->toDateString() }}</dd>

                    <dt class="col-6">{{ __('Monthly Amount') }}</dt>
                    <dd class="col-6">{{ number_format((float) $assistance->amount_per_period, 2) }} {{ $currency }}</dd>

                    <dt class="col-6">{{ __('Planned Total') }}</dt>
                    <dd class="col-6">{{ number_format((float) $stats['planned_amount'], 2) }} {{ $currency }}</dd>

                    <dt class="col-6">{{ __('Paid Total') }}</dt>
                    <dd class="col-6 mb-0">{{ number_format((float) $stats['paid_amount'], 2) }} {{ $currency }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        @include('admin.case_assistances.components.show.details-card')
    </div>

    <div class="col-lg-4">
        @include('admin.case_assistances.components.show.beneficiary-card')
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
                @foreach ($installments as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row->due_at->toDateString() }}</td>
                        <td>{{ number_format((float) $row->amount, 2) }} {{ $currency }}</td>
                        <td><span class="badge bg-label-{{ $row->status()->color() }}">{{ $row->status()->label() }}</span></td>
                        <td>{{ $row->paid_at?->toDateTimeString() ?? '—' }}</td>
                        <td>{{ $row->cancelled_at?->toDateTimeString() ?? '—' }}</td>
                        <td class="text-wrap">{{ $row->notes ?? '—' }}</td>
                        @if ($canCancelInstallment)
                            <td>
                                @if ($row->isPending())
                                    <button type="button" class="btn btn-sm btn-label-danger waves-effect"
                                        data-bs-toggle="modal" data-bs-target="#cancel-installment-modal"
                                        data-cancel-url="{{ route('admin.case_assistances.cancel_installment', [$assistance, $row]) }}"
                                        data-cancel-label="{{ $row->due_at->toDateString() }}">
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
