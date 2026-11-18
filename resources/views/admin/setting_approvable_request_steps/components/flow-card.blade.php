{{-- One request type's flow as a numbered chain of reviewers. --}}
@php
    $arrow = ($admin_language ?? 'en') === 'en' ? 'right' : 'left';
    $lastIndex = count($flow['roles']) - 1;
@endphp
<div class="col-md-6 col-xl-4" id="approval-flow-card-{{ $flow['type']->value }}">
    <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">{{ $flow['type']->label() }}</h6>
            @if ($route_edit)
                <button type="button" class="btn btn-sm btn-label-primary" data-bs-toggle="modal"
                    data-bs-target="#approval-flow-modal-{{ $flow['type']->value }}">
                    <i class="ti ti-pencil me-1"></i> {{ __('Edit Flow') }}
                </button>
            @endif
        </div>
        <div class="card-body">
            @if ($flow['roles'] === [])
                <span class="text-muted">{{ __('No steps set — the direct manager then HR review it.') }}</span>
            @else
                <div class="d-flex flex-wrap align-items-center gap-2">
                    @foreach ($flow['roles'] as $index => $role)
                        <span class="badge rounded-pill {{ $index === $lastIndex ? 'bg-primary' : 'bg-label-secondary' }} px-3 py-2">
                            {{ $index + 1 }}. {{ $role->label() }}
                            @if ($index === $lastIndex)
                                <i class="ti ti-gavel ms-1" title="{{ __('Final decision') }}"></i>
                            @endif
                        </span>
                        @if ($index !== $lastIndex)
                            <i class="ti ti-arrow-narrow-{{ $arrow }} text-muted"></i>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
