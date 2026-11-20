{{--
    Quick-create a beneficiary without leaving the assistance form. It posts
    to charity_cases.store as JSON; on success form-js adds the new profile to
    the beneficiary select and selects it. The full form link does the same
    through a page round trip (`return_to`).
--}}
<div class="modal fade" id="quick-case-modal" tabindex="-1" aria-labelledby="quick-case-title" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            {{ html()->form('POST', route('admin.charity_cases.store'))->id('quick-case-form')->attributes(['data-duplicate-check' => '', 'data-ignore-id' => '', 'novalidate' => true])->open() }}
                <div class="modal-header">
                    <h5 class="modal-title" id="quick-case-title">{{ __('New Beneficiary') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger d-none" data-quick-case-errors role="alert"></div>
                    @include('admin.charity_cases.components.fields', ['plainSelects' => true, 'charityCase' => null])
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <a href="{{ route('admin.charity_cases.create', ['return_to' => 'assistance']) }}" class="btn btn-link px-0">
                        {{ __('Open the full form') }}
                    </a>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary waves-effect">{{ __('Save and Select') }}</button>
                    </div>
                </div>
            {{ html()->form()->close() }}
        </div>
    </div>
</div>
