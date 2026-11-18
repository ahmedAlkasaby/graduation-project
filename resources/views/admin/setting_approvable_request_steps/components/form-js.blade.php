<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const template = document.getElementById('approval-flow-step-template');
        const roleCount = template.content.querySelectorAll('option').length;

        /** Wires one flow dialog: add, move, remove, renumber, and undo on cancel. */
        function setUpFlowEditor(modal) {
            const list = modal.querySelector('.approval-flow-steps');
            const addButton = modal.querySelector('.approval-flow-add');
            const saveButton = modal.querySelector('.approval-flow-save');
            const duplicateWarning = modal.querySelector('.approval-flow-duplicate');
            const form = modal.querySelector('.approval-flow-form');
            const errorBox = modal.querySelector('.approval-flow-errors');
            let savedRows = [];
            let isSaving = false;

            const steps = () => Array.from(list.querySelectorAll('.approval-flow-step'));
            const chosenRoles = () => steps().map(row => row.querySelector('.approval-flow-role').value);

            function addStep() {
                const chosen = chosenRoles();
                const row = template.content.firstElementChild.cloneNode(true);
                const select = row.querySelector('.approval-flow-role');
                const free = Array.from(select.options).find(option => !chosen.includes(option.value));

                if (free) {
                    select.value = free.value;
                }
                list.appendChild(row);
            }

            /** Renumbers the steps, marks the final one and disables what cannot be done. */
            function refresh() {
                const rows = steps();
                const chosen = chosenRoles();
                const hasDuplicate = new Set(chosen).size !== chosen.length;

                rows.forEach(function(row, index) {
                    const isLast = index === rows.length - 1;
                    const role = chosen[index];

                    row.querySelector('.approval-flow-number').textContent = index + 1;
                    row.querySelector('.approval-flow-final').classList.toggle('d-none', !isLast);
                    row.querySelector('.approval-flow-up').disabled = index === 0;
                    row.querySelector('.approval-flow-down').disabled = isLast;
                    row.querySelector('.approval-flow-remove').disabled = rows.length === 1;

                    row.querySelectorAll('.approval-flow-role option').forEach(function(option) {
                        option.disabled = option.value !== role && chosen.includes(option.value);
                    });
                });

                addButton.disabled = rows.length >= roleCount;
                saveButton.disabled = isSaving || rows.length === 0 || hasDuplicate;
                duplicateWarning.classList.toggle('d-none', !hasDuplicate);
            }

            /** Remembers the rows as they are when the dialog opens, to put them back on cancel. */
            function snapshot() {
                savedRows = steps().map(row => row.querySelector('.approval-flow-role').value);
            }

            function restore() {
                list.innerHTML = '';
                savedRows.forEach(function(role) {
                    addStep();
                    list.lastElementChild.querySelector('.approval-flow-role').value = role;
                });
                refresh();
            }

            addButton.addEventListener('click', function() {
                addStep();
                refresh();
            });

            list.addEventListener('change', refresh);

            list.addEventListener('click', function(event) {
                const button = event.target.closest('button');
                if (!button) {
                    return;
                }
                const row = button.closest('.approval-flow-step');

                if (button.classList.contains('approval-flow-up') && row.previousElementSibling) {
                    list.insertBefore(row, row.previousElementSibling);
                } else if (button.classList.contains('approval-flow-down') && row.nextElementSibling) {
                    list.insertBefore(row.nextElementSibling, row);
                } else if (button.classList.contains('approval-flow-remove')) {
                    row.remove();
                }
                refresh();
            });

            function showErrors(messages) {
                errorBox.replaceChildren(...messages.map(function(message) {
                    const line = document.createElement('div');
                    line.textContent = message;
                    return line;
                }));
                errorBox.classList.toggle('d-none', messages.length === 0);
            }

            /** Saves over AJAX, then swaps in the re-rendered card and closes the dialog. */
            form.addEventListener('submit', async function(event) {
                event.preventDefault();
                if (isSaving) {
                    return;
                }
                isSaving = true;
                showErrors([]);
                refresh();

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    const data = await response.json().catch(() => ({}));

                    if (response.ok && data.success) {
                        document.getElementById(modal.dataset.cardId).outerHTML = data.card;
                        snapshot();
                        bootstrap.Modal.getOrCreateInstance(modal).hide();
                        window.toastr && toastr.success(data.message);
                    } else if (response.status === 422 && data.errors) {
                        showErrors(Object.values(data.errors).flat());
                    } else {
                        showErrors([data.message || @json(__('Something went wrong, please try again'))]);
                    }
                } catch (error) {
                    showErrors([@json(__('Something went wrong, please try again'))]);
                } finally {
                    isSaving = false;
                    refresh();
                }
            });

            modal.addEventListener('show.bs.modal', snapshot);
            modal.addEventListener('hidden.bs.modal', function() {
                restore();
                showErrors([]);
            });

            if (steps().length === 0) {
                addStep();
            }
            refresh();

            if (modal.dataset.openOnLoad === '1') {
                bootstrap.Modal.getOrCreateInstance(modal).show();
            }
        }

        document.querySelectorAll('.approval-flow-modal').forEach(setUpFlowEditor);
    });
</script>
