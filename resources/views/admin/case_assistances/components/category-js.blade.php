{{-- The quick-create category dialog: saves a category and selects it in #category-select. --}}
<script>
    $(function () {
        const quickForm = document.getElementById('quick-category-form');
        if (!quickForm) {
            return;
        }

        const failed = @json(__('Something went wrong. Please try again.'));
        const modal = document.getElementById('quick-category-modal');
        const errorsBox = quickForm.querySelector('[data-quick-category-errors]');
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const $category = $('#category-select');

        function showErrors(messages) {
            errorsBox.replaceChildren(...messages.map(function (message) {
                const line = document.createElement('div');
                line.textContent = message;
                return line;
            }));
            errorsBox.classList.remove('d-none');
        }

        quickForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const button = quickForm.querySelector('[type="submit"]');
            button.disabled = true;

            fetch(quickForm.action, {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': token },
                body: new FormData(quickForm),
            })
                .then(async function (response) {
                    const data = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        showErrors(data.errors ? Object.values(data.errors).flat() : [data.message || failed]);
                        return;
                    }

                    $category.append(new Option(data.category.text, data.category.id, true, true)).trigger('change');
                    bootstrap.Modal.getOrCreateInstance(modal).hide();
                    quickForm.reset();
                    errorsBox.classList.add('d-none');
                })
                .catch(() => showErrors([failed]))
                .finally(() => (button.disabled = false));
        });
    });
</script>
