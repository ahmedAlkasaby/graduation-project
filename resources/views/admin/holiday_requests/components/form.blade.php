@include('admin.layouts.forms.head', [
    'show_name' => false,
])
<div class="row g-3 mt-2 mb-4">
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Employee Id'),
            'select_name' => 'employee_id',
            'select_value' => isset($holiday_request) ? $holiday_request->employee_id : null,
            'select_function' => $employees ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Holiday Type'),
            'select_name' => 'type',
            'select_value' => isset($holiday_request) ? $holiday_request->type?->value : null,
            'select_function' => $holidayTypes ?? [],
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date', [
            'date_name' => 'start_date',
            'date_value' => isset($holiday_request) ? $holiday_request->start_date : null,
            'label_name' => __('Start Date'),

        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date', [
            'date_name' => 'end_date',
            'date_value' => isset($holiday_request) ? $holiday_request->end_date : null,
            'label_name' => __('End Date'),

        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'days',
            'number_value' => isset($holiday_request) ? $holiday_request->days : 0,
            'label_name' => __('Days'),

        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Reason Id'),
            'select_name' => 'reason_id',
            'select_value' => isset($holiday_request) ? $holiday_request->reason_id : null,
            'select_function' => $reasons ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'status',
            'text_value' => isset($holiday_request) ? $holiday_request->status : null,
            'label_name' => __('Status'),

        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Reviewed By'),
            'select_name' => 'reviewed_by',
            'select_value' => isset($holiday_request) ? $holiday_request->reviewed_by : null,
            'select_function' => $reviewers ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date-time', [
            'date_name' => 'reviewed_at',
            'date_value' => isset($holiday_request) ? $holiday_request->reviewed_at : null,
            'label_name' => __('Reviewed At'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Rejection Reason Id'),
            'select_name' => 'rejection_reason_id',
            'select_value' => isset($holiday_request) ? $holiday_request->rejection_reason_id : null,
            'select_function' => $rejectionReasons ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'order_id',
            'number_value' => isset($holiday_request) ? $holiday_request->order_id : 0,
            'label_name' => __('Order Id'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.selects.active')
    </div>
</div>
@include('admin.layouts.forms.footer')

