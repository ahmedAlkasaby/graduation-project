{{--
    The new-assistance form's behaviour:
    - the beneficiary select searches profiles by name, national ID or phone;
    - choosing a type shows (and enables) only that type's fields, so the
      other type's fields are never submitted;
    - a live summary shows the installments that will be generated, mirroring
      CaseAssistanceService::dueDates() (monthly, on the 1st, inclusive,
      12 by default);
    - the quick-create dialog saves a profile and selects it.
--}}
<script>
    $(function () {
        const strings = {
            placeholder: @json(__('Search by name, national ID or phone.')),
            oneTime: @json(__('1 installment of :amount :currency, due on :date.')),
            recurring: @json(__(':count monthly installments × :amount :currency = :total :currency, from :start to :end (on the 1st of each month).')),
            endBeforeStart: @json(__('The end month must be the start month or later.')),
            tooMany: @json(__('A monthly assistance can have at most :count installments.', ['count' => $maxInstallments])),
            failed: @json(__('Something went wrong. Please try again.')),
        };
        const currency = @json($currency);
        const defaultInstallments = {{ (int) $defaultInstallments }};
        const maxInstallments = {{ (int) $maxInstallments }};
        const locale = document.documentElement.lang || undefined;

        // Beneficiary select
        const $beneficiary = $('#beneficiary-select');
        $beneficiary.select2({
            width: '100%',
            placeholder: strings.placeholder,
            ajax: {
                url: @json(route('admin.charity_cases.lookup')),
                dataType: 'json',
                delay: 250,
                data: (params) => ({ search: params.term || '' }),
                processResults: (data) => data,
            },
        });

        // Type switch
        const form = document.querySelector('form.assistance-form');
        const sections = form.querySelectorAll('[data-type-section]');
        const summary = document.getElementById('schedule-summary');
        const value = (name) => (form.querySelector('[name="' + name + '"]')?.value || '').trim();
        const selectedType = () => form.querySelector('input[name="assistance_type"]:checked')?.value;

        function applyType() {
            const type = selectedType();

            sections.forEach(function (section) {
                const active = section.dataset.typeSection === type;
                section.classList.toggle('d-none', !active);
                section.querySelectorAll('input, select, textarea').forEach((input) => (input.disabled = !active));
            });

            form.querySelectorAll('.custom-option').forEach(function (option) {
                option.classList.toggle('checked', option.querySelector('input').checked);
            });

            updateSummary();
        }

        // Schedule summary
        const money = (amount) => Number(amount).toLocaleString(locale, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const monthIndex = (month) => {
            const match = /^(\d{4})-(\d{2})$/.exec(month);
            return match ? Number(match[1]) * 12 + Number(match[2]) - 1 : null;
        };
        const monthLabel = (index) => new Date(Math.floor(index / 12), index % 12, 1)
            .toLocaleDateString(locale, { month: 'long', year: 'numeric' });
        const fill = (text, values) => Object.keys(values).reduce((out, key) => out.split(':' + key).join(values[key]), text);

        function show(text, level) {
            summary.textContent = text;
            summary.className = 'alert alert-' + level + ' mt-4 mb-0';
        }

        function updateSummary() {
            summary.classList.add('d-none');

            if (selectedType() === 'one_time') {
                const amount = value('requested_amount');
                const dueAt = value('due_at');
                if (amount && dueAt) {
                    show(fill(strings.oneTime, { amount: money(amount), currency: currency, date: dueAt }), 'primary');
                }
                return;
            }

            const amount = value('amount_per_period');
            const start = monthIndex(value('start_month'));
            if (!amount || start === null) {
                return;
            }

            const endInput = monthIndex(value('end_month'));
            const end = endInput === null ? start + defaultInstallments - 1 : endInput;
            const count = end - start + 1;

            if (count < 1) {
                show(strings.endBeforeStart, 'danger');
                return;
            }
            if (count > maxInstallments) {
                show(strings.tooMany, 'danger');
                return;
            }

            show(fill(strings.recurring, {
                count: count,
                amount: money(amount),
                total: money(amount * count),
                currency: currency,
                start: monthLabel(start),
                end: monthLabel(end),
            }), 'primary');
        }

        form.querySelectorAll('input[name="assistance_type"]').forEach((input) => input.addEventListener('change', applyType));
        ['requested_amount', 'due_at', 'amount_per_period', 'start_month', 'end_month'].forEach(function (name) {
            form.querySelector('[name="' + name + '"]')?.addEventListener('input', updateSummary);
        });
        applyType();

        // Quick-create a beneficiary
        const quickForm = document.getElementById('quick-case-form');
        if (!quickForm) {
            return;
        }

        const modal = document.getElementById('quick-case-modal');
        const errorsBox = quickForm.querySelector('[data-quick-case-errors]');
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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
                        showErrors(data.errors ? Object.values(data.errors).flat() : [data.message || strings.failed]);
                        return;
                    }

                    $beneficiary.append(new Option(data.case.text, data.case.id, true, true)).trigger('change');
                    bootstrap.Modal.getOrCreateInstance(modal).hide();
                    quickForm.reset();
                    errorsBox.classList.add('d-none');
                    quickForm.dispatchEvent(new Event('duplicate-check:reset'));
                })
                .catch(() => showErrors([strings.failed]))
                .finally(() => (button.disabled = false));
        });
    });
</script>
