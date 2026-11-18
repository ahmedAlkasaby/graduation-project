{{-- Stop task modal: the user writes why the task is being stopped --}}
<div class="modal fade" id="stopModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" id="stopForm" autocomplete="off">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Stop Task') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('admin.layouts.forms.fields.hidden', ['hidden' => 'task_id'])
                <div class="alert alert-danger d-none form-errors"></div>
                @include('admin.layouts.forms.fields.textarea', [
                    'text_name' => 'feedback',
                    'text_id' => 'stop-feedback',
                    'label_name' => __('Feedback'),
                    'label_req' => true,
                    'rows' => 4,
                    'placeholder' => __('Why are you stopping this task?'),
                ])
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-warning">
                    <i class="ti ti-player-pause me-1"></i>{{ __('Stop') }}
                </button>
            </div>
        </form>
    </div>
</div>
