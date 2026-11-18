{{-- One step of the flow editor. The number and the final badge are kept current by form-js. --}}
<li class="list-group-item d-flex align-items-center gap-3 approval-flow-step">
    <span class="badge bg-label-primary rounded-circle approval-flow-number">1</span>
    <select name="roles[]" class="form-select approval-flow-role" required>
        @foreach ($roleOptions as $value => $label)
            <option value="{{ $value }}" @selected($value === $selectedRole)>{{ $label }}</option>
        @endforeach
    </select>
    <span class="badge bg-primary text-nowrap d-none approval-flow-final">
        <i class="ti ti-gavel me-1"></i>{{ __('Final decision') }}
    </span>
    <div class="btn-group">
        <button type="button" class="btn btn-icon btn-outline-secondary approval-flow-up" title="{{ __('Move Up') }}">
            <i class="ti ti-arrow-up"></i>
        </button>
        <button type="button" class="btn btn-icon btn-outline-secondary approval-flow-down" title="{{ __('Move Down') }}">
            <i class="ti ti-arrow-down"></i>
        </button>
        <button type="button" class="btn btn-icon btn-outline-danger approval-flow-remove" title="{{ __('Remove') }}">
            <i class="ti ti-trash"></i>
        </button>
    </div>
</li>
