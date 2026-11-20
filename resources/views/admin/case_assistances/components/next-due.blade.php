@if ($assistance->nextDueAt())
    <span class="{{ $assistance->isNextDueOverdue() ? 'text-danger fw-medium' : '' }}">{{ $assistance->nextDueAt()->toDateString() }}</span>
    @if ($assistance->isNextDueOverdue())
        <span class="badge bg-label-danger ms-1">{{ __('Overdue') }}</span>
    @endif
@else
    <span class="text-muted">—</span>
@endif
