@include('admin.layouts.forms.head', [
    'show_name' => true,
])
@if (request('from') === 'board')
    <input type="hidden" name="from" value="board">
@endif
<div class="row g-3 mt-2 mb-4">
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Project Id'),
            'select_name' => 'project_id',
            'select_value' => isset($task) ? $task->project_id : request('project_id'),
            'select_function' => $projects ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Folder Id'),
            'select_name' => 'folder_id',
            'select_value' => isset($task) ? $task->folder_id : request('folder_id'),
            'select_function' => $folders ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'title',
            'text_value' => isset($task) ? $task->title : null,
            'label_name' => __('Title'),
            
        ])
    </div>
    <div class="col-md-12">
        @include('admin.layouts.forms.fields.textarea', [
            'text_name' => 'description',
            'text_value' => isset($task) ? $task->description : null,
            'label_name' => __('Description'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Priority'),
            'select_name' => 'priority',
            'select_value' => isset($task) ? $task->priority : 'normal',
            'select_function' => $priorities ?? [],
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Status'),
            'select_name' => 'task_status_id',
            'select_value' => isset($task) ? $task->task_status_id : request('task_status_id'),
            'not_req' => true,
            'select_function' => $statuses ?? [],
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date', [
            'date_name' => 'due_date',
            'date_class' => 'flatpickr-datetime',
            'date_value' => isset($task) ? $task->due_date?->format('Y-m-d H:i') : null,
            'label_name' => __('Due Date'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'estimated_minutes',
            'number_value' => isset($task) ? $task->estimated_minutes : 0,
            'label_name' => __('Estimated Minutes'),
            
        ])
    </div>
    @if (isset($task))
        {{-- calculated when the task is completed (end - start, minus stops); not editable --}}
        <div class="col-md-6">
            @include('admin.layouts.forms.fields.number', [
                'number_name' => 'time_tracked_minutes_display',
                'number_value' => $task->time_tracked_minutes,
                'label_name' => __('Time Tracked Minutes'),
                'disable' => true,
                'not_req' => true,
            ])
        </div>
    @endif
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.multi_select', [
            'label_name' => __('Assignees'),
            'select_name' => 'assignee_ids',
            'select_value' => isset($task) ? $task->assigneeIds() : [],
            'select_function' => $assignees ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Created By'),
            'select_name' => 'created_by',
            'select_value' => isset($task) ? $task->created_by : auth()->id(),
            'select_function' => ['' => __('None')] + ($createdBies ?? []),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'order_id',
            'number_value' => isset($task) ? $task->order_id : 0,
            'label_name' => __('Order Id'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.selects.active')
    </div>
</div>
@include('admin.layouts.forms.footer')

