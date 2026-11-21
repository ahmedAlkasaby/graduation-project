{{--
    Quick-create a category without leaving the assistance form. It posts to
    case_categories.store as JSON; on success category-js adds the new category
    to the category select and selects it.
--}}
<div class="modal fade" id="quick-category-modal" tabindex="-1" aria-labelledby="quick-category-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            {{ html()->form('POST', route('admin.case_categories.store'))->id('quick-category-form')->attribute('novalidate', true)->open() }}
                <div class="modal-header">
                    <h5 class="modal-title" id="quick-category-title">{{ __('New Category') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger d-none" data-quick-category-errors role="alert"></div>
                    <input type="hidden" name="active" value="1">
                    <div class="mb-3">
                        <label class="form-label" for="quick-category-name-en">{{ __('English Name') }}</label>
                        <input type="text" class="form-control" id="quick-category-name-en" name="name[en]" maxlength="255">
                    </div>
                    <div class="mb-0">
                        <label class="form-label" for="quick-category-name-ar">{{ __('Arabic Name') }}</label>
                        <input type="text" class="form-control" id="quick-category-name-ar" name="name[ar]" maxlength="255">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary waves-effect">{{ __('Save and Select') }}</button>
                </div>
            {{ html()->form()->close() }}
        </div>
    </div>
</div>
