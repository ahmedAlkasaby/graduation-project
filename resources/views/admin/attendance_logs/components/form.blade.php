@include('admin.layouts.forms.head', [
    'show_name' => false,
])
<div class="row g-3 mt-2 mb-4">
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Employee Id'),
            'select_name' => 'employee_id',
            'select_value' => isset($attendance_log) ? $attendance_log->employee_id : null,
            'select_function' => $employees ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'type',
            'text_value' => isset($attendance_log) ? $attendance_log->type : null,
            'label_name' => __('Type'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date-time', [
            'date_name' => 'recorded_at',
            'date_value' => isset($attendance_log) ? $attendance_log->recorded_at : null,
            'label_name' => __('Recorded At'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'qr_token',
            'text_value' => isset($attendance_log) ? $attendance_log->qr_token : null,
            'label_name' => __('Qr Token'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'latitude',
            'number_value' => isset($attendance_log) ? $attendance_log->latitude : 0,
            'label_name' => __('Latitude'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'longitude',
            'number_value' => isset($attendance_log) ? $attendance_log->longitude : 0,
            'label_name' => __('Longitude'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'distance_from_company',
            'number_value' => isset($attendance_log) ? $attendance_log->distance_from_company : 0,
            'label_name' => __('Distance From Company'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'device_identifier',
            'text_value' => isset($attendance_log) ? $attendance_log->device_identifier : null,
            'label_name' => __('Device Identifier'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'source',
            'text_value' => isset($attendance_log) ? $attendance_log->source : null,
            'label_name' => __('Source'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Daily Attendance Id'),
            'select_name' => 'daily_attendance_id',
            'select_value' => isset($attendance_log) ? $attendance_log->daily_attendance_id : null,
            'select_function' => $dailyAttendances ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'ip_address',
            'text_value' => isset($attendance_log) ? $attendance_log->ip_address : null,
            'label_name' => __('Ip Address'),
            
        ])
    </div>
    <div class="col-md-12">
        @include('admin.layouts.forms.fields.textarea', [
            'text_name' => 'user_agent',
            'text_value' => isset($attendance_log) ? $attendance_log->user_agent : null,
            'label_name' => __('User Agent'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'order_id',
            'number_value' => isset($attendance_log) ? $attendance_log->order_id : 0,
            'label_name' => __('Order Id'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.selects.active')
    </div>
</div>
@include('admin.layouts.forms.footer')

