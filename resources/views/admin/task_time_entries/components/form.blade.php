@include('admin.layouts.forms.head', [
    'show_name' => true,
])
<div class="row g-3 mt-2 mb-4">
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Task Id'),
            'select_name' => 'task_id',
            'select_value' => isset($task_time_entry) ? $task_time_entry->task_id : null,
            'select_function' => $tasks ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Employee Id'),
            'select_name' => 'employee_id',
            'select_value' => isset($task_time_entry) ? $task_time_entry->employee_id : null,
            'select_function' => $employees ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date-time', [
            'date_name' => 'started_at',
            'date_value' => isset($task_time_entry) ? $task_time_entry->started_at : null,
            'label_name' => __('Started At'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date-time', [
            'date_name' => 'stopped_at',
            'date_value' => isset($task_time_entry) ? $task_time_entry->stopped_at : null,
            'label_name' => __('Stopped At'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'minutes',
            'number_value' => isset($task_time_entry) ? $task_time_entry->minutes : 0,
            'label_name' => __('Minutes'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Reason Id'),
            'select_name' => 'reason_id',
            'select_value' => isset($task_time_entry) ? $task_time_entry->reason_id : null,
            'select_function' => $reasons ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'order_id',
            'number_value' => isset($task_time_entry) ? $task_time_entry->order_id : 0,
            'label_name' => __('Order Id'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.selects.active')
    </div>
</div>
@include('admin.layouts.forms.footer')

