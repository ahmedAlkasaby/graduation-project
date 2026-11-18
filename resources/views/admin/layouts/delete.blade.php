<!-- Cancel Modal -->
<div class="modal fade" id="cancel-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h4 class="modal-title">{{ __('Cancel Order') }}</h4>
            </div>
            <div class="modal-body">
                {{ __('Confirm Cancel ?') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">{{ __('No') }}</button>
                <button id="confirm-cancel" type="button" class="btn btn-danger">{{ __('Yes') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="delete-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h4 class="modal-title">{{ __('Delete') }}</h4>
            </div>
            <div class="modal-body">
                {{ __('Confirm Delete ?') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">{{ __('No') }}</button>
                <button id="confirm-delete" type="button" class="btn btn-danger">{{ __('Yes') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Restore Modal -->
<div class="modal fade" id="restore-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h4 class="modal-title">{{ __('Restore Data') }}</h4>
            </div>
            <div class="modal-body">
                {{ __('Confirm Restore Data ?') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">{{ __('No') }}</button>
                <button id="confirm-restore" type="button" class="btn btn-danger">{{ __('Yes') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Force Delete Modal -->
<div class="modal fade" id="force-delete-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h4 class="modal-title">{{ __('Delete') }}</h4>
            </div>
            <div class="modal-body">
                {{ __('Confirm Delete ?') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">{{ __('No') }}</button>
                <button id="confirm-force-delete" type="button" class="btn btn-danger">{{ __('Yes') }}</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
// Path to sound file
const soundPath = "{{ asset('js/sharp.mp3') }}";

document.addEventListener("DOMContentLoaded", function () {
    document.body.addEventListener("click", function (event) {
        // DELETE
        if (event.target.id === "delete") {
            let deleteModal = new bootstrap.Modal(document.getElementById("delete-modal"));
            deleteModal.show();
            document.getElementById("confirm-delete").setAttribute("data-id", event.target.getAttribute("data-id"));
        }

        if (event.target.id === "confirm-delete") {
            const deleteId = event.target.getAttribute("data-id");

            // Play sound first (user click triggers it)
            playSound();

            let deleteModalEl = document.getElementById("delete-modal");
            let deleteModal = bootstrap.Modal.getInstance(deleteModalEl);
            deleteModal.hide();

            document.querySelector(`[data-delete-id="${deleteId}"]`)?.click();
            showToastr("Deleted", "Item #" + deleteId + " has been deleted.");
        }

        // FORCE DELETE
        if (event.target.id === "force-delete") {
            let forceDeleteModal = new bootstrap.Modal(document.getElementById("force-delete-modal"));
            forceDeleteModal.show();
            document.getElementById("confirm-force-delete").setAttribute("data-id", event.target.getAttribute("data-id"));
        }

        if (event.target.id === "confirm-force-delete") {
            const forceDeleteId = event.target.getAttribute("data-id");

            playSound();

            let forceDeleteModalEl = document.getElementById("force-delete-modal");
            let forceDeleteModal = bootstrap.Modal.getInstance(forceDeleteModalEl);
            forceDeleteModal.hide();

            document.querySelector(`[data-force-delete-id="${forceDeleteId}"]`)?.click();
            showToastr("Deleted", "Item #" + forceDeleteId + " has been permanently deleted.");
        }

        // RESTORE
        if (event.target.id === "restore") {
            let restoreModal = new bootstrap.Modal(document.getElementById("restore-modal"));
            restoreModal.show();
            document.getElementById("confirm-restore").setAttribute("data-id", event.target.getAttribute("data-id"));
        }

        if (event.target.id === "confirm-restore") {
            const restoreId = event.target.getAttribute("data-id");

            playSound();

            let restoreModalEl = document.getElementById("restore-modal");
            let restoreModal = bootstrap.Modal.getInstance(restoreModalEl);
            restoreModal.hide();

            document.querySelector(`[data-restore-id="${restoreId}"]`)?.click();
            showToastr("Restored", "Item #" + restoreId + " has been restored.");
        }

        // CANCEL (optional toast)
        if (event.target.id === "confirm-cancel") {
            const cancelId = event.target.getAttribute("data-id");
            playSound();

            let cancelModalEl = document.getElementById("cancel-modal");
            let cancelModal = bootstrap.Modal.getInstance(cancelModalEl);
            cancelModal.hide();

            document.querySelector(`[data-cancel-id="${cancelId}"]`)?.click();
            showToastr("Cancelled", "Order #" + cancelId + " has been cancelled.");
        }
    });
});

// Toastr function
function showToastr(title, message) {
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 5000
    };
    toastr.info(message, title);
}

// Play sound
function playSound() {
    const audio = new Audio(soundPath);
    audio.play().catch(e => console.warn("Sound play blocked:", e));
}
</script>
