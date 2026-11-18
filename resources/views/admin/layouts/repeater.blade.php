<!-- REPEATER DELETE MODAL -->
<div class="modal fade" id="repeater-delete-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('Delete') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{ __('Are you sure you want to delete this item?') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('No') }}</button>
                <button id="repeater-confirm-delete" type="button" class="btn btn-danger">{{ __('Yes') }}</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var language = $('body').attr('data-language');

    /**
     * Re-initialise Select2 inside a freshly cloned repeater item.
     *
     * A clone carries Select2's leftovers — the rendered container, the
     * select2-hidden-accessible class and the data-select2-id attributes — so
     * Select2 treats the element as already initialised and quietly does
     * nothing. Every trace has to go before it will attach again.
     */
    function reinitSelect2($scope) {
        $scope.find('select.select2').each(function() {
            const $select = $(this);

            if ($select.data('select2')) {
                $select.select2('destroy');
            }

            $select.siblings('.select2-container').remove();
            $select.removeData('select2');
            $select.removeClass('select2-hidden-accessible')
                .removeAttr('data-select2-id tabindex aria-hidden');
            $select.find('option').removeAttr('data-select2-id');

            $select.select2(language === 'ar' ? { dir: 'rtl' } : {});
        });
    }

    /** Blank out a cloned item so it does not repeat the values it came from. */
    function resetFields($scope) {
        $scope.find('input[type="text"], textarea').val('');
        $scope.find('input[type="number"]').val(0);
        $scope.find('input[name="id"], input[name$="[id]"]').not('.index_number').val(0);

        $scope.find('select').each(function() {
            const selectName = $(this).attr('name') || '';

            if (selectName.includes('[active]')) {
                $(this).val('1');
            } else if (selectName.includes('[is_offer]')) {
                $(this).val('0');
            } else {
                // -1 is the "nothing chosen" option on attribute selects
                $(this).val('-1');
            }
        });
    }

    /** Drop the cloned gallery so a new group starts empty. */
    function resetDropzone($group) {
        const $dropzone = $group.find('.dropzone');
        if (!$dropzone.length) {
            return;
        }

        Dropzone.instances
            .filter(dz => dz.element === $dropzone[0])
            .forEach(dz => dz.destroy());

        $dropzone.find('.dz-preview').remove();
        $dropzone.removeClass('dz-started dz-max-files-reached dz-clickable');
        $dropzone.addClass('dz-clickable');
        $dropzone.find('.dz-message').show();

        // the clone inherited the previous group's images
        $dropzone.removeAttr('data-existing-images data-existing-sizes');
        $group.find('input[type="file"]').val('');
        $group.find('input[type="hidden"][name*="[gallery_images]"]').val('');
    }

    /**
     * Keep each group's gallery pointing at its own slot.
     *
     * The repeater renumbers name attributes when a group is added or removed,
     * but it knows nothing about the dropzone's inputs or the ids tying them
     * together. Left alone they keep the old index, and a gallery ends up filed
     * under a group that no longer holds those rows.
     */
    function reindexVariantGroups() {
        $('.variant-groups-repeater > [data-repeater-list] > [data-repeater-item]').each(function(index) {
            const $group = $(this);

            $group.find('.index_number').first().val(index);

            const $dropzone = $group.find('.dropzone[data-dropzone-multi]').first();
            if (!$dropzone.length) {
                return;
            }

            const fileId = 'input-multi-group-' + index;
            const existingId = 'existing-group-' + index;

            $dropzone.attr('data-input-id', fileId).attr('data-existing-id', existingId);

            $group.find('input[type="file"]').first()
                .attr('id', fileId)
                .attr('name', 'variant_groups[' + index + '][gallery_files][]');

            $group.find('input[type="hidden"][name*="[gallery_images]"]').first()
                .attr('id', existingId)
                .attr('name', 'variant_groups[' + index + '][gallery_images]');
        });
    }

    $(document).ready(function() {

        let repeaterDeleteTarget = null;
        let repeaterDeleteFn = null;

        // CLICK CONFIRM DELETE REPEATER
        $('#repeater-confirm-delete').on('click', function() {
            if (repeaterDeleteTarget && repeaterDeleteFn) {
                // Actually delete the item now
                repeaterDeleteFn.call(repeaterDeleteTarget);
                setTimeout(() => {
                    reindexVariantGroups();
                }, 300);
            }

            // Close modal
            let modalEl = document.getElementById("repeater-delete-modal");
            let modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();

            repeaterDeleteTarget = null;
            repeaterDeleteFn = null;
        });

        // VARIANT GROUPS, EACH WITH ITS NESTED ROWS
        $('.variant-groups-repeater').repeater({
            defaultValues: {
                'price': 0,
                'offer_price': 0,
                'active': 1,
                'is_offer': 0,
                'max_amount': 0,
                'id': 0,
                'image_group_id': 0,
            },
            show: function() {
                const $group = $(this);
                $group.slideDown();

                // the clone arrives carrying the previous group's data
                resetFields($group);
                $group.find('input[name$="[image_group_id]"]').val(0);

                // a new group starts with a single blank row
                const $rows = $group.find('[data-repeater-list="rows"]');
                $rows.find('[data-repeater-item]').slice(1).remove();
                resetFields($rows.find('[data-repeater-item]').first());

                reinitSelect2($group);
                resetDropzone($group);
                reindexVariantGroups();

                // the clone has no Dropzone attached until it is asked for
                if (window.initDropzoneUploads) {
                    window.initDropzoneUploads($group[0]);
                }
            },
            hide: function(deleteElement) {
                // prevent default deletion
                repeaterDeleteTarget = this;
                repeaterDeleteFn = deleteElement;

                // show modal
                let modalEl = document.getElementById("repeater-delete-modal");
                let modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            },
            isFirstItemUndeletable: true,
            repeaters: [{
                selector: '.variant-rows-repeater',
                defaultValues: {
                    'price': 0,
                    'offer_price': 0,
                    'active': 1,
                    'is_offer': 0,
                    'max_amount': 0,
                    'id': 0,
                },
                show: function() {
                    const $row = $(this);
                    $row.slideDown();

                    // a new row starts empty rather than repeating the cloned one
                    resetFields($row);
                    reinitSelect2($row);
                },
                hide: function(deleteElement) {
                    // prevent default deletion for nested repeater
                    repeaterDeleteTarget = this;
                    repeaterDeleteFn = deleteElement;

                    let modalEl = document.getElementById("repeater-delete-modal");
                    let modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                },
                isFirstItemUndeletable: true
            }]
        });

    });
</script>
