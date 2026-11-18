@include('admin.layouts.forms.head', [
    'show_name' => false,
])
<div class="row g-3 mt-2 mb-4">
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Employee Id'),
            'select_name' => 'employee_id',
            'select_value' => isset($daily_attendance) ? $daily_attendance->employee_id : null,
            'select_function' => $employees ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date', [
            'date_name' => 'attendance_date',
            'date_value' => isset($daily_attendance) ? $daily_attendance->attendance_date : null,
            'label_name' => __('Attendance Date'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date-time', [
            'date_name' => 'check_in',
            'date_value' => isset($daily_attendance) ? $daily_attendance->check_in : null,
            'label_name' => __('Check In'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date-time', [
            'date_name' => 'check_out',
            'date_value' => isset($daily_attendance) ? $daily_attendance->check_out : null,
            'label_name' => __('Check Out'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'scheduled_work_minutes',
            'number_value' => isset($daily_attendance) ? $daily_attendance->scheduled_work_minutes : 0,
            'label_name' => __('Scheduled Work Minutes'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'scheduled_break_minutes',
            'number_value' => isset($daily_attendance) ? $daily_attendance->scheduled_break_minutes : 0,
            'label_name' => __('Scheduled Break Minutes'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'latest_check_in',
            'number_value' => isset($daily_attendance) ? $daily_attendance->latest_check_in : 0,
            'label_name' => __('Latest Check In'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Permission Requests'),
            'select_name' => 'permission_request_ids',
            'is_multiple' => true,
            'select_value' => isset($daily_attendance) ? $daily_attendance->permissionRequests->modelKeys() : [],
            'select_function' => $permissionRequests ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date-time', [
            'date_name' => 'expected_check_out',
            'date_value' => isset($daily_attendance) ? $daily_attendance->expected_check_out : null,
            'label_name' => __('Expected Check Out'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'break_minutes',
            'number_value' => isset($daily_attendance) ? $daily_attendance->break_minutes : 0,
            'label_name' => __('Break Minutes'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'worked_minutes',
            'number_value' => isset($daily_attendance) ? $daily_attendance->worked_minutes : 0,
            'label_name' => __('Worked Minutes'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'late_minutes',
            'number_value' => isset($daily_attendance) ? $daily_attendance->late_minutes : 0,
            'label_name' => __('Late Minutes'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'early_leave_minutes',
            'number_value' => isset($daily_attendance) ? $daily_attendance->early_leave_minutes : 0,
            'label_name' => __('Early Leave Minutes'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'overtime_minutes',
            'number_value' => isset($daily_attendance) ? $daily_attendance->overtime_minutes : 0,
            'label_name' => __('Overtime Minutes'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Status'),
            'label_req' => true,
            'select_name' => 'status',
            'select_value' => isset($daily_attendance) ? $daily_attendance->status : null,
            'select_function' => \App\Enums\DailyAttendanceStatusEnum::options(),
        ])
    </div>
    <div class="col-md-12">
        @include('admin.layouts.forms.fields.textarea', [
            'text_name' => 'notes',
            'text_value' => isset($daily_attendance) ? $daily_attendance->notes : null,
            'label_name' => __('Notes'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'order_id',
            'number_value' => isset($daily_attendance) ? $daily_attendance->order_id : 0,
            'label_name' => __('Order Id'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.selects.active')
    </div>
</div>
@include('admin.layouts.forms.footer')

