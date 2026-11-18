@include('admin.layouts.forms.head', [
    'show_name' => true,
])
<div class="row g-3 mt-2 mb-4">
    <div class="col-md-12">
        @include('admin.layouts.forms.fields.textarea', [
            'text_name' => 'description',
            'text_value' => isset($project) ? $project->description : null,
            'label_name' => __('Description'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label' => __('Status'),
            'select_name' => 'status',
            'select_value' => isset($project) ? $project->status : null,
            'select_function' => defaultOption() + ($statuses ?? []),
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date', [
            'date_name' => 'deadline',
            'date_value' => isset($project) ? $project->deadline?->format('Y-m-d') : null,
            'label_name' => __('Deadline'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date', [
            'date_name' => 'started_at',
            'date_class' => 'flatpickr-datetime',
            'date_value' => isset($project) ? $project->started_at?->format('Y-m-d H:i') : null,
            'label_name' => __('Started At'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date', [
            'date_name' => 'ended_at',
            'date_class' => 'flatpickr-datetime',
            'date_value' => isset($project) ? $project->ended_at?->format('Y-m-d H:i') : null,
            'label_name' => __('Ended At'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Owner Id'),
            'select_name' => 'owner_id',
            'select_value' => isset($project) ? $project->owner_id : null,
            'select_function' => $owners ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'order_id',
            'number_value' => isset($project) ? $project->order_id : 0,
            'label_name' => __('Order Id'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.selects.active', ['label_name' => __('Active')])
    </div>
</div>
@include('admin.layouts.forms.footer')

