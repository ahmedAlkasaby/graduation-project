{{-- Generic confirm modal (same look as admin.layouts.delete); the board JS sets the title, body and button --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirm-title"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="confirm-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('No') }}</button>
                <button type="button" class="btn btn-danger" id="confirm-ok">{{ __('Yes') }}</button>
            </div>
        </div>
    </div>
</div>
