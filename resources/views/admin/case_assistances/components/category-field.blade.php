{{--
    The category select, shared by the create and edit forms. The quick-create
    button shows when $canQuickCreateCategory is true (case_categories.store).
--}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Category') }}</h5>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-end gap-3">
            <div class="flex-grow-1">
                @include('admin.layouts.forms.fields.select', [
                    'label_req' => true,
                    'label' => __('Category'),
                    'select_name' => 'category_id',
                    'select_id' => 'category-select',
                    'select_function' => ['' => __('Select')] + $categories,
                    'select_value' => old('category_id', $assistance->category_id ?? null),
                ])
            </div>
            @if ($canQuickCreateCategory)
                <button type="button" class="btn btn-outline-primary waves-effect mb-1" data-bs-toggle="modal" data-bs-target="#quick-category-modal">
                    <i class="ti ti-plus me-1"></i>{{ __('New Category') }}
                </button>
            @endif
        </div>
    </div>
</div>
