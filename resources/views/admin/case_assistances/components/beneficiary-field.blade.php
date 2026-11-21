{{--
    The searchable beneficiary select, shared by the create and edit forms.
    beneficiary-js turns it into a select2 that searches profiles by name,
    national ID or phone. The quick-create button shows when
    $canQuickCreateCase is true.
--}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Beneficiary') }}</h5>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-end gap-3">
            <div class="flex-grow-1">
                @include('admin.layouts.forms.fields.select', [
                    'label_req' => true,
                    'label' => __('Beneficiary Profile'),
                    'select_name' => 'case_id',
                    'select_id' => 'beneficiary-select',
                    'select_class' => 'form-select',
                    'select_function' => $selectedCase ? [$selectedCase->id => $selectedCase->selectLabel()] : [],
                    'select_value' => $selectedCase?->id,
                ])
            </div>
            @if ($canQuickCreateCase)
                <button type="button" class="btn btn-outline-primary waves-effect mb-1" data-bs-toggle="modal" data-bs-target="#quick-case-modal">
                    <i class="ti ti-user-plus me-1"></i>{{ __('New Beneficiary') }}
                </button>
            @endif
        </div>
        <small class="text-muted">{{ __('Search by name, national ID or phone.') }}</small>
    </div>
</div>
