{{--
    A one-time assistance is a single payment: one card for that payment
    (amount, due date, whether it is paid), next to what it is and who it is
    for. No counters or installments table: there is only ever one.
--}}
<div class="row g-4 mb-4">
    {{-- The payment --}}
    <div class="col-lg-4">
        <div class="card h-100 border-top border-3 border-{{ $installment?->status()->color() ?? 'secondary' }}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ __('Payment') }}</h5>
                @if ($installment)
                    <span class="badge bg-label-{{ $installment->status()->color() }}">{{ $installment->status()->label() }}</span>
                @endif
            </div>
            <div class="card-body">
                @if ($installment)
                    <div class="text-muted small">{{ __('Amount') }}</div>
                    <h3 class="mb-3">{{ number_format((float) $installment->amount, 2) }} {{ $currency }}</h3>

                    <div class="text-muted small">{{ __('Due Date') }}</div>
                    <div class="fw-medium">{{ $installment->due_at->toDateString() }}</div>
                    @if ($installment->isPending())
                        <small class="{{ $installment->isOverdue() ? 'text-danger' : 'text-muted' }}">
                            {{ $installment->due_at->diffForHumans() }}
                        </small>
                    @endif

                    @if ($installment->isPaid())
                        <div class="text-muted small mt-3">{{ __('Paid At') }}</div>
                        <div class="fw-medium">{{ $installment->paid_at->toDateTimeString() }}</div>
                    @endif

                    @if ($installment->isCancelled())
                        <div class="text-muted small mt-3">{{ __('Cancelled At') }}</div>
                        <div class="fw-medium">{{ $installment->cancelled_at->toDateTimeString() }}</div>
                    @endif

                    @if ($installment->notes)
                        <div class="text-muted small mt-3">{{ __('Notes') }}</div>
                        <div class="text-wrap">{{ $installment->notes }}</div>
                    @endif

                    @if ($canCancelInstallment && $installment->isPending())
                        <button type="button" class="btn btn-label-danger waves-effect w-100 mt-4"
                            data-bs-toggle="modal" data-bs-target="#cancel-installment-modal"
                            data-cancel-url="{{ route('admin.case_assistances.cancel_installment', [$assistance, $installment]) }}"
                            data-cancel-label="{{ $installment->due_at->toDateString() }}">
                            <i class="ti ti-ban me-1"></i>{{ __('Cancel Payment') }}
                        </button>
                    @endif

                    <small class="text-muted d-block mt-3">{{ __('Payments are recorded by the payments module, not here.') }}</small>
                @else
                    <span class="text-muted">—</span>
                @endif
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
