{{--
    Duplicate warnings for every [data-duplicate-check] box on the page: as the
    national ID, phone or name is filled in, existing profiles that match are
    listed under the fields. A national ID match blocks saving (the server
    refuses it); a phone or similar name is only a warning.
--}}
<script>
    (function () {
        const url = @json(route('admin.charity_cases.duplicates'));
        const strings = {
            national_id: @json(__('A case with this national ID already exists:')),
            phone: @json(__('This phone is already used by:')),
            similar_names: @json(__('Cases with a similar name:')),
        };
        const levels = { national_id: 'danger', phone: 'warning', similar_names: 'info' };

        document.querySelectorAll('[data-duplicate-check]').forEach(function (box) {
            const output = box.querySelector('[data-duplicate-warnings]');
            const field = (name) => box.querySelector('[name="' + name + '"]');
            let timer = null;

            function render(data) {
                output.replaceChildren();

                Object.keys(levels).forEach(function (key) {
                    const cases = (data && data[key]) || [];
                    if (cases.length === 0) {
                        return;
                    }

                    const alert = document.createElement('div');
                    alert.className = 'alert alert-' + levels[key] + ' mb-2 py-2';
                    const title = document.createElement('div');
                    title.className = 'fw-medium mb-1';
                    title.textContent = strings[key];
                    alert.appendChild(title);

                    cases.forEach(function (match) {
                        const link = document.createElement('a');
                        link.href = match.url;
                        link.target = '_blank';
                        link.className = 'd-block';
                        link.textContent = [match.full_name, match.national_id, match.phone].filter(Boolean).join(' — ');
                        alert.appendChild(link);
                    });

                    output.appendChild(alert);
                });
            }

            function check() {
                const params = new URLSearchParams({
                    national_id: (field('national_id')?.value || '').trim(),
                    phone: (field('phone')?.value || '').trim(),
                    full_name: (field('full_name')?.value || '').trim(),
                    ignore_id: box.dataset.ignoreId || '',
                });

                fetch(url + '?' + params.toString(), { headers: { Accept: 'application/json' } })
                    .then((response) => (response.ok ? response.json() : null))
                    .then(render)
                    .catch(() => {});
            }

            ['national_id', 'phone', 'full_name'].forEach(function (name) {
                field(name)?.addEventListener('input', function () {
                    clearTimeout(timer);
                    timer = setTimeout(check, 500);
                });
            });

            box.addEventListener('duplicate-check:reset', () => output.replaceChildren());
        });
    })();
</script>
