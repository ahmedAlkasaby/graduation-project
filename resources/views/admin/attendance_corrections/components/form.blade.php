@include('admin.layouts.forms.head', [
    'show_name' => false,
])
<div class="row g-3 mt-2 mb-4">
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Employee Id'),
            'select_name' => 'employee_id',
            'select_value' => isset($attendance_correction) ? $attendance_correction->employee_id : null,
            'select_function' => $employees ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Attendance Id'),
            'select_name' => 'attendance_id',
            'select_value' => isset($attendance_correction) ? $attendance_correction->attendance_id : null,
            'select_function' => $attendances ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date', [
            'date_name' => 'requested_date',
            'date_value' => isset($attendance_correction) ? $attendance_correction->requested_date : null,
            'label_name' => __('Requested Date'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date-time', [
            'date_name' => 'requested_check_in',
            'date_value' => isset($attendance_correction) ? $attendance_correction->requested_check_in : null,
            'label_name' => __('Requested Check In'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date-time', [
            'date_name' => 'requested_check_out',
            'date_value' => isset($attendance_correction) ? $attendance_correction->requested_check_out : null,
            'label_name' => __('Requested Check Out'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Reason Id'),
            'select_name' => 'reason_id',
            'select_value' => isset($attendance_correction) ? $attendance_correction->reason_id : null,
            'select_function' => $reasons ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'status',
            'text_value' => isset($attendance_correction) ? $attendance_correction->status : null,
            'label_name' => __('Status'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Approved By'),
            'select_name' => 'approved_by',
            'select_value' => isset($attendance_correction) ? $attendance_correction->approved_by : null,
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date-time', [
            'date_name' => 'approved_at',
            'date_value' => isset($attendance_correction) ? $attendance_correction->approved_at : null,
            'label_name' => __('Approved At'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Rejection Reason Id'),
            'select_name' => 'rejection_reason_id',
            'select_value' => isset($attendance_correction) ? $attendance_correction->rejection_reason_id : null,
            'select_function' => $rejectionReasons ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'order_id',
            'number_value' => isset($attendance_correction) ? $attendance_correction->order_id : 0,
            'label_name' => __('Order Id'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.selects.active')
    </div>
</div>
@include('admin.layouts.forms.footer')

