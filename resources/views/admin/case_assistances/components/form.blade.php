{{--
    The new-assistance form, in the order an admin works through it: who it
    is for, what kind of support, what it is, then the money and dates. Only
    the selected type's amount and date fields are shown and submitted.
--}}

{{-- 1. Beneficiary --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Beneficiary') }}</h5>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-end gap-3">
            <div class="flex-grow-1">
                @include('admin.layouts.forms.fields.select', [
                    'label_req' => true,
                    'label' => __('Shared Case'),
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

{{-- 2. Type --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Assistance Type') }}</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach ($types as $type)
                <div class="col-md-6">
                    <div class="form-check custom-option custom-option-basic h-100 @if ($selectedType === $type->value) checked @endif">
                        <label class="form-check-label custom-option-content" for="assistance-type-{{ $type->value }}">
                            <input name="assistance_type" class="form-check-input" type="radio" value="{{ $type->value }}"
                                id="assistance-type-{{ $type->value }}" @checked($selectedType === $type->value)>
                            <span class="custom-option-header">
                                <span class="h6 mb-0">{{ $type->label() }}</span>
                            </span>
                            <span class="custom-option-body">
                                <small>{{ $type->description() }}</small>
                            </span>
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- 3. Details --}}
@include('admin.case_assistances.components.details-fields')

{{-- 4. Amount and schedule --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Amount and Schedule') }}</h5>
        <small class="text-muted">{{ __('These cannot be changed after the assistance is created.') }}</small>
    </div>
    <div class="card-body">
        <div class="row g-3" data-type-section="one_time">
            <div class="col-sm-6">
                @include('admin.layouts.forms.fields.number', [
                    'not_req' => true,
                    'label_req' => true,
                    'label' => __('Amount') . ' (' . $currency . ')',
                    'number_name' => 'requested_amount',
                    'step' => '0.01',
                    'min' => '0.01',
                ])
            </div>
            <div class="col-sm-6">
                @include('admin.layouts.forms.fields.text', [
                    'not_req' => true,
                    'label_req' => true,
                    'label_name' => __('Due Date'),
                    'text_name' => 'due_at',
                    'date_type' => 'date',
                ])
            </div>
        </div>

        <div class="row g-3" data-type-section="recurring">
            <div class="col-sm-4">
                @include('admin.layouts.forms.fields.number', [
                    'not_req' => true,
                    'label_req' => true,
                    'label' => __('Monthly Amount') . ' (' . $currency . ')',
                    'number_name' => 'amount_per_period',
                    'step' => '0.01',
                    'min' => '0.01',
                ])
            </div>
            <div class="col-sm-4">
                @include('admin.layouts.forms.fields.text', [
                    'not_req' => true,
                    'label_req' => true,
                    'label_name' => __('Installments Start Month'),
                    'text_name' => 'start_month',
                    'date_type' => 'month',
                    'placeholder' => 'YYYY-MM',
                ])
            </div>
            <div class="col-sm-4">
                @include('admin.layouts.forms.fields.text', [
                    'not_req' => true,
                    'label_name' => __('Installments End Month'),
                    'text_name' => 'end_month',
                    'date_type' => 'month',
                    'placeholder' => 'YYYY-MM',
                ])
                <small class="text-muted">{{ __('Optional. Leave empty for :count monthly installments.', ['count' => $defaultInstallments]) }}</small>
            </div>
        </div>

        <div class="alert alert-primary mt-4 mb-0 d-none" id="schedule-summary" role="status"></div>
    </div>
</div>

<div class="mb-4">
    @include('admin.layouts.forms.buttons.form-save', ['save' => __('Create Assistance')])
</div>
