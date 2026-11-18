@include('admin.layouts.forms.head', [
    'show_name' => false,
])
<div class="row g-3 mt-2 mb-4">
    <div class="col-md-12">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'name',
            'text_value' => isset($task_folder) ? $task_folder->name : null,
            'label_name' => __('Name'),
            'label_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Project Id'),
            'select_name' => 'project_id',
            'select_value' => isset($task_folder) ? $task_folder->project_id : null,
            'select_function' => $projects ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'order_id',
            'number_value' => isset($task_folder) ? $task_folder->order_id : 0,
            'label_name' => __('Order Id'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.selects.active')
    </div>
</div>
@include('admin.layouts.forms.footer')

