@include('admin.layouts.forms.head', [
    'show_name' => false,
])
<div class="row g-3 mt-2 mb-4">
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Employee Id'),
            'select_name' => 'employee_id',
            'select_value' => isset($remote_request) ? $remote_request->employee_id : null,
            'select_function' => $employees ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date', [
            'date_name' => 'remote_date',
            'date_value' => isset($remote_request) ? $remote_request->remote_date : null,
            'label_name' => __('Remote Date'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'start_time',
            'text_value' => isset($remote_request) ? $remote_request->start_time : null,
            'label_name' => __('Start Time'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'end_time',
            'text_value' => isset($remote_request) ? $remote_request->end_time : null,
            'label_name' => __('End Time'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Reason Id'),
            'select_name' => 'reason_id',
            'select_value' => isset($remote_request) ? $remote_request->reason_id : null,
            'select_function' => $reasons ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-12">
        @include('admin.layouts.forms.fields.textarea', [
            'text_name' => 'notes',
            'text_value' => isset($remote_request) ? $remote_request->notes : null,
            'label_name' => __('Notes'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'status',
            'text_value' => isset($remote_request) ? $remote_request->status : null,
            'label_name' => __('Status'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Approved By'),
            'select_name' => 'approved_by',
            'select_value' => isset($remote_request) ? $remote_request->approved_by : null,
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date-time', [
            'date_name' => 'approved_at',
            'date_value' => isset($remote_request) ? $remote_request->approved_at : null,
            'label_name' => __('Approved At'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Rejection Reason Id'),
            'select_name' => 'rejection_reason_id',
            'select_value' => isset($remote_request) ? $remote_request->rejection_reason_id : null,
            'select_function' => $rejectionReasons ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'order_id',
            'number_value' => isset($remote_request) ? $remote_request->order_id : 0,
            'label_name' => __('Order Id'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.selects.active')
    </div>
</div>
@include('admin.layouts.forms.footer')

