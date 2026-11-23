{{--
    Makes the region select follow the city select. Works on any element with
    [data-address-fields][data-regions][data-choose] that holds a
    [data-address-city] and a [data-address-region] select: data-regions is
    `city id => [{id, name}]` and data-choose is the empty option's label.
    Safe to include more than once on a page.
--}}
<script>
    (function () {
        if (window.addressFieldsReady) {
            return;
        }
        window.addressFieldsReady = true;

        // Rebuilds the region select with the regions of the chosen city.
        function fillRegions(group) {
            const city = group.querySelector('[data-address-city]');
            const region = group.querySelector('[data-address-region]');
            const regions = JSON.parse(group.dataset.regions)[city.value] || [];
            const selected = region.value;

            region.replaceChildren(new Option(group.dataset.choose, ''));
            regions.forEach(function (item) {
                const isSelected = String(item.id) === selected;
                region.append(new Option(item.name, item.id, isSelected, isSelected));
            });
        }

        document.addEventListener('change', function (event) {
            if (event.target.matches('[data-address-city]')) {
                fillRegions(event.target.closest('[data-address-fields]'));
            }
        });

        // A reset (the quick-create dialog after saving) puts the city back, so the regions follow it.
        document.addEventListener('reset', function (event) {
            setTimeout(function () {
                event.target.querySelectorAll('[data-address-fields]').forEach(fillRegions);
            });
        });
    })();
</script>
