{{--
    Confirms cancelling one installment, with an optional note. Each row's
    Cancel button carries its own URL; the modal points the form at it.
--}}
<div class="modal fade" id="cancel-installment-modal" tabindex="-1" aria-labelledby="cancel-installment-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            {{ html()->form('PATCH', '#')->id('cancel-installment-form')->open() }}
                <div class="modal-header">
                    <h5 class="modal-title" id="cancel-installment-title">{{ __('Cancel Installment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        {{ __('Cancel the installment due on') }} <strong data-cancel-label></strong>?
                        {{ __('A cancelled installment can no longer be paid, and this cannot be undone.') }}
                    </p>
                    @include('admin.layouts.forms.fields.textarea', [
                        'not_req' => true,
                        'label_name' => __('Note (optional)'),
                        'text_name' => 'notes',
                        'rows' => 3,
                    ])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('Back') }}</button>
                    <button type="submit" class="btn btn-danger waves-effect">{{ __('Cancel Installment') }}</button>
                </div>
            {{ html()->form()->close() }}
        </div>
    </div>
</div>
<script>
    document.getElementById('cancel-installment-modal').addEventListener('show.bs.modal', function (event) {
        const trigger = event.relatedTarget;
        this.querySelector('#cancel-installment-form').action = trigger.dataset.cancelUrl;
        this.querySelector('[data-cancel-label]').textContent = trigger.dataset.cancelLabel;
    });
</script>
