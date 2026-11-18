{{--
    The dialog that edits one request type's whole flow as an ordered list. A
    step's place in the list is its order; the last step makes the final decision.
    form-js saves it over AJAX; without JS it posts normally and reopens by
    itself when that save failed validation.
--}}
<div class="modal fade approval-flow-modal" id="approval-flow-modal-{{ $flow['type']->value }}" tabindex="-1"
    aria-labelledby="approval-flow-modal-title-{{ $flow['type']->value }}" aria-hidden="true"
    data-open-on-load="{{ $flow['isOpen'] ? '1' : '0' }}" data-card-id="approval-flow-card-{{ $flow['type']->value }}">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            {!! html()->form('PUT')->route('admin.setting_approvable_request_steps.update', $flow['type']->value)->class('approval-flow-form')->attribute('autocomplete', 'off')->open() !!}
            <input type="hidden" name="request_type" value="{{ $flow['type']->value }}">

            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="approval-flow-modal-title-{{ $flow['type']->value }}">
                        {{ __('Approval Flow') }}: {{ $flow['type']->label() }}
                    </h5>
                    <small class="text-muted">
                        {{ __('Steps run from top to bottom. Use the arrows to reorder them. The last step makes the final decision.') }}
                    </small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-danger approval-flow-errors {{ $flow['isOpen'] && $errors->any() ? '' : 'd-none' }}" role="alert">
                    @if ($flow['isOpen'])
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    @endif
                </div>

                <ul class="list-group mb-3 approval-flow-steps">
                    @foreach ($flow['roleValues'] as $role)
                        @include('admin.setting_approvable_request_steps.components.step-row', ['selectedRole' => $role])
                    @endforeach
                </ul>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <button type="button" class="btn btn-label-primary approval-flow-add">
                        <i class="ti ti-plus me-1"></i> {{ __('Add Step') }}
                    </button>
                    <small class="text-danger d-none approval-flow-duplicate">
                        {{ __('Each role can appear only once in the flow.') }}
                    </small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary approval-flow-save">{{ __('Save') }}</button>
            </div>
            {!! html()->form()->close() !!}
        </div>
    </div>
</div>
