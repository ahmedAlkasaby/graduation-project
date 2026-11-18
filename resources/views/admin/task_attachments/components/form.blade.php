@include('admin.layouts.forms.head', [
    'show_name' => true,
])
<div class="row g-3 mt-2 mb-4">
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Task Id'),
            'select_name' => 'task_id',
            'select_value' => isset($task_attachment) ? $task_attachment->task_id : null,
            'select_function' => $tasks ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'file_name',
            'text_value' => isset($task_attachment) ? $task_attachment->file_name : null,
            'label_name' => __('File Name'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'file_path',
            'text_value' => isset($task_attachment) ? $task_attachment->file_path : null,
            'label_name' => __('File Path'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'file_type',
            'text_value' => isset($task_attachment) ? $task_attachment->file_type : null,
            'label_name' => __('File Type'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'file_size',
            'number_value' => isset($task_attachment) ? $task_attachment->file_size : 0,
            'label_name' => __('File Size'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Uploaded By'),
            'select_name' => 'uploaded_by',
            'select_value' => isset($task_attachment) ? $task_attachment->uploaded_by : null,
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'order_id',
            'number_value' => isset($task_attachment) ? $task_attachment->order_id : 0,
            'label_name' => __('Order Id'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.selects.active')
    </div>
</div>
@include('admin.layouts.forms.footer')

