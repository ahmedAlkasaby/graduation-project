@include('admin.layouts.forms.head', [
    'show_name' => true,
])
<div class="row g-3 mt-2 mb-4">
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Task Id'),
            'select_name' => 'task_id',
            'select_value' => isset($task_comment) ? $task_comment->task_id : null,
            'select_function' => $tasks ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Author Id'),
            'select_name' => 'author_id',
            'select_value' => isset($task_comment) ? $task_comment->author_id : null,
            'select_function' => $authors ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-12">
        @include('admin.layouts.forms.fields.textarea', [
            'text_name' => 'body',
            'text_value' => isset($task_comment) ? $task_comment->body : null,
            'label_name' => __('Body'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Is System'),
            'select_name' => 'is_system',
            'select_value' => isset($task_comment) ? $task_comment->is_system : null,
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'order_id',
            'number_value' => isset($task_comment) ? $task_comment->order_id : 0,
            'label_name' => __('Order Id'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.selects.active')
    </div>
</div>
@include('admin.layouts.forms.footer')

