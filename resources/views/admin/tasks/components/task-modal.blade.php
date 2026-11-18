{{-- Task modal (create / edit) --}}
<div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content" id="taskForm" autocomplete="off">
            <div class="modal-header">
                <h5 class="modal-title" id="taskModalTitle">{{ __('New Task') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('admin.layouts.forms.fields.hidden', ['hidden' => 'id'])
                @include('admin.layouts.forms.fields.hidden', ['hidden' => 'folder_id'])
                @include('admin.layouts.forms.fields.hidden', ['hidden' => 'project_id'])
                <div class="alert alert-danger d-none form-errors"></div>
                <div class="row g-3">
                    <div class="col-12">
                        @include('admin.layouts.forms.fields.text', [
                            'text_name' => 'title',
                            'text_id' => 'task-title',
                            'label_name' => __('Title'),
                            'label_req' => true,
                        ])
                    </div>
                    <div class="col-12">
                        @include('admin.layouts.forms.fields.textarea', [
                            'text_name' => 'description',
                            'text_id' => 'task-description',
                            'label_name' => __('Description'),
                            'rows' => 3,
                            'not_req' => true,
                        ])
                    </div>
                    <div class="col-md-4">
                        @include('admin.layouts.forms.fields.select', [
                            'label_name' => __('Status'),
                            'select_name' => 'task_status_id',
                            'select_id' => 'task-status',
                            'select_function' => $statuses,
                        ])
                    </div>
                    <div class="col-md-4">
                        @include('admin.layouts.forms.fields.select', [
                            'label_name' => __('Priority'),
                            'select_name' => 'priority',
                            'select_id' => 'task-priority',
                            'select_value' => 'normal',
                            'select_function' => $priorities,
                        ])
                    </div>
                    <div class="col-md-4">
                        @include('admin.layouts.forms.fields.date', [
                            'date_name' => 'due_date',
                            'date_id' => 'task-due-date',
                            'date_class' => 'flatpickr-datetime',
                            'label_name' => __('Due Date'),
                            'label_req' => true,
                        ])
                    </div>
                    <div class="col-md-6">
                        @include('admin.layouts.forms.fields.multi_select', [
                            'label_name' => __('Assignees'),
                            'select_name' => 'assignee_ids',
                            'select_id' => 'task-assignees',
                            'select_function' => $assignees,
                            'not_req' => true,
                        ])
                    </div>
                    <div class="col-md-6">
                        @include('admin.layouts.forms.fields.number', [
                            'number_name' => 'estimated_minutes',
                            'number_id' => 'task-estimated',
                            'number_value' => 0,
                            'label_name' => __('Estimated Minutes'),
                            'min' => 0,
                            'not_req' => true,
                        ])
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('Attachments') }}</label>
                        @include('admin.layouts.forms.dropzone.dropzone-multi', [
                            'name' => 'attachments',
                        ])
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <div>
                    @if ($route_delete)
                        <button type="button" class="btn btn-label-danger d-none" id="task-delete">
                            <i class="ti ti-trash me-1"></i>{{ __('Delete') }}
                        </button>
                    @endif
                </div>
                <div>
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
