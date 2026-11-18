@include('admin.layouts.forms.head', [
    'show_name' => false,
])
<div class="row g-3 mt-2 mb-4">
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Employee Id'),
            'select_name' => 'employee_id',
            'select_value' => isset($increase_request) ? $increase_request->employee_id : null,
            'select_function' => $employees ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'current_salary',
            'number_value' => isset($increase_request) ? $increase_request->current_salary : 0,
            'label_name' => __('Current Salary'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'requested_salary',
            'number_value' => isset($increase_request) ? $increase_request->requested_salary : 0,
            'label_name' => __('Requested Salary'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Reason Id'),
            'select_name' => 'reason_id',
            'select_value' => isset($increase_request) ? $increase_request->reason_id : null,
            'select_function' => $reasons ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'status',
            'text_value' => isset($increase_request) ? $increase_request->status : null,
            'label_name' => __('Status'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Approved By'),
            'select_name' => 'approved_by',
            'select_value' => isset($increase_request) ? $increase_request->approved_by : null,
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date-time', [
            'date_name' => 'approved_at',
            'date_value' => isset($increase_request) ? $increase_request->approved_at : null,
            'label_name' => __('Approved At'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Rejection Reason Id'),
            'select_name' => 'rejection_reason_id',
            'select_value' => isset($increase_request) ? $increase_request->rejection_reason_id : null,
            'select_function' => $rejectionReasons ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'order_id',
            'number_value' => isset($increase_request) ? $increase_request->order_id : 0,
            'label_name' => __('Order Id'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.selects.active')
    </div>
</div>
@include('admin.layouts.forms.footer')

