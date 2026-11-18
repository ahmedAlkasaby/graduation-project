@include('admin.layouts.forms.head', [
    'show_name' => true,
])
<div class="row g-3 mt-2 mb-4">
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Created By'),
            'select_name' => 'created_by',
            'select_value' => isset($reminder) ? $reminder->created_by : null,
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'title',
            'text_value' => isset($reminder) ? $reminder->title : null,
            'label_name' => __('Title'),
        ])
    </div>
    <div class="col-md-12">
        @include('admin.layouts.forms.fields.textarea', [
            'text_name' => 'description',
            'text_value' => isset($reminder) ? $reminder->description : null,
            'label_name' => __('Description'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Category'),
            'select_name' => 'category',
            'select_value' => isset($reminder) ? $reminder->category : null,
            'select_function' => \App\Enums\ReminderCategoryEnum::options(),
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date-time', [
            'date_name' => 'remind_at',
            'date_value' => isset($reminder) ? $reminder->remind_at : null,
            'label_name' => __('Remind At'),
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Repeat'),
            'select_name' => 'repeat_rule',
            'select_value' => isset($reminder) ? $reminder->repeat_rule?->value : \App\Enums\ReminderRepeatEnum::None->value,
            'select_function' => \App\Enums\ReminderRepeatEnum::options(),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date', [
            'date_name' => 'repeat_until',
            'date_value' => isset($reminder) ? $reminder->repeat_until : null,
            'label_name' => __('Repeat Until'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Audience'),
            'select_name' => 'audience',
            'select_value' => isset($reminder) ? $reminder->audience?->value : null,
            'select_function' => \App\Enums\ReminderAudienceEnum::options(),
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Status'),
            'select_name' => 'status',
            'select_value' => isset($reminder) ? $reminder->status : \App\Enums\ReminderRecipientStatusEnum::Upcoming->value,
            'select_function' => \App\Enums\ReminderRecipientStatusEnum::options(),
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Department Id'),
            'select_name' => 'department_id',
            'select_value' => isset($reminder) ? $reminder->department_id : null,
            'select_function' => $departments ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Employee Id'),
            'select_name' => 'employee_id',
            'select_value' => isset($reminder) ? $reminder->employee_id : null,
            'select_function' => $employees ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'order_id',
            'number_value' => isset($reminder) ? $reminder->order_id : 0,
            'label_name' => __('Order Id'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.selects.active')
    </div>
</div>
@include('admin.layouts.forms.footer')
