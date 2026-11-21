{{--
    Confirms cancelling a whole assistance, one-time or recurring. It says what
    will happen (how many unpaid installments and how much) before the admin
    commits, and takes an optional note stored on each cancelled installment.
--}}
<div class="modal fade" id="cancel-assistance-modal" tabindex="-1" aria-labelledby="cancel-assistance-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            {{ html()->form('PATCH', route('admin.case_assistances.cancel', $assistance))->open() }}
                <div class="modal-header">
                    <h5 class="modal-title" id="cancel-assistance-title">{{ __('Cancel Assistance') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning d-flex gap-2" role="alert">
                        <i class="ti ti-alert-triangle mt-1"></i>
                        <div>
                            @if ($assistance->isRecurring())
                                {{ trans_choice(':count unpaid installment (:amount :currency) will be cancelled.|:count unpaid installments (:amount :currency) will be cancelled.', $stats['unpaid'], ['count' => $stats['unpaid'], 'amount' => number_format((float) $stats['unpaid_amount'], 2), 'currency' => $currency]) }}
                                @if ($stats['paid'] > 0)
                                    {{ __('Installments that are already paid are not affected.') }}
                                @endif
                            @else
                                {{ __('The payment of :amount :currency will be cancelled.', ['amount' => number_format((float) $stats['unpaid_amount'], 2), 'currency' => $currency]) }}
                            @endif
                            {{ __('This cannot be undone.') }}
                        </div>
                    </div>
                    @include('admin.layouts.forms.fields.textarea', [
                        'not_req' => true,
                        'label_name' => __('Note (optional)'),
                        'text_name' => 'notes',
                        'rows' => 3,
                    ])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('Back') }}</button>
                    <button type="submit" class="btn btn-danger waves-effect">{{ __('Cancel Assistance') }}</button>
                </div>
            {{ html()->form()->close() }}
        </div>
    </div>
</div>
