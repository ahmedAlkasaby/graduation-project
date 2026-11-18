@include('admin.layouts.forms.head', [
    'show_name' => false,
])
<div class="row g-3 mt-2 mb-4">
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Employee Id'),
            'select_name' => 'employee_id',
            'select_value' => isset($permission_request) ? $permission_request->employee_id : null,
            'select_function' => $employees ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date', [
            'date_name' => 'permission_date',
            'date_value' => isset($permission_request) ? $permission_request->permission_date : null,
            'label_name' => __('Permission Date'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'from_time',
            'text_value' => isset($permission_request) ? $permission_request->from_time : null,
            'label_name' => __('From Time'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'to_time',
            'text_value' => isset($permission_request) ? $permission_request->to_time : null,
            'label_name' => __('To Time'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'minutes',
            'number_value' => isset($permission_request) ? $permission_request->minutes : 0,
            'label_name' => __('Minutes'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'period',
            'text_value' => isset($permission_request) ? $permission_request->period : null,
            'label_name' => __('Period'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Reason Id'),
            'select_name' => 'reason_id',
            'select_value' => isset($permission_request) ? $permission_request->reason_id : null,
            'select_function' => $reasons ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'status',
            'text_value' => isset($permission_request) ? $permission_request->status : null,
            'label_name' => __('Status'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Approved By'),
            'select_name' => 'approved_by',
            'select_value' => isset($permission_request) ? $permission_request->approved_by : null,
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date-time', [
            'date_name' => 'approved_at',
            'date_value' => isset($permission_request) ? $permission_request->approved_at : null,
            'label_name' => __('Approved At'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Rejection Reason Id'),
            'select_name' => 'rejection_reason_id',
            'select_value' => isset($permission_request) ? $permission_request->rejection_reason_id : null,
            'select_function' => $rejectionReasons ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'order_id',
            'number_value' => isset($permission_request) ? $permission_request->order_id : 0,
            'label_name' => __('Order Id'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.selects.active')
    </div>
</div>
@include('admin.layouts.forms.footer')

