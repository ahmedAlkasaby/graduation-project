{{-- Folder modal (create / edit) --}}
<div class="modal fade" id="folderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" id="folderForm" autocomplete="off">
            <div class="modal-header">
                <h5 class="modal-title" id="folderModalTitle">{{ __('New Folder') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('admin.layouts.forms.fields.hidden', ['hidden' => 'id'])
                <div class="alert alert-danger d-none form-errors"></div>
                <div class="row g-3">
                    <div class="col-md-12">
                        @include('admin.layouts.forms.fields.text', [
                            'text_name' => 'name',
                            'text_id' => 'folder-name',
                            'label_name' => __('Name'),
                            'label_req' => true,
                        ])
                    </div>
                    <div class="col-md-6">
                        @include('admin.layouts.forms.fields.select', [
                            'label_name' => __('Project'),
                            'select_name' => 'project_id',
                            'select_id' => 'folder-project',
                            'select_function' => ['' => __('None')] + $projectOptions,
                            'not_req' => true,
                        ])
                    </div>
                    <div class="col-md-6">
                        @include('admin.layouts.forms.fields.select', [
                            'label_name' => __('Parent Folder'),
                            'select_name' => 'parent_id',
                            'select_id' => 'folder-parent',
                            'select_function' => ['' => __('None')] + $parentOptions,
                            'not_req' => true,
                        ])
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </div>
        </form>
    </div>
</div>
