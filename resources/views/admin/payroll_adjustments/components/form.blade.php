@include('admin.layouts.forms.head', [
    'show_name' => true,
])
<div class="row g-3 mt-2 mb-4">
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Employee Id'),
            'select_name' => 'employee_id',
            'select_value' => isset($payroll_adjustment) ? $payroll_adjustment->employee_id : null,
            'select_function' => $employees ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'type',
            'text_value' => isset($payroll_adjustment) ? $payroll_adjustment->type : null,
            'label_name' => __('Type'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'amount',
            'number_value' => isset($payroll_adjustment) ? $payroll_adjustment->amount : 0,
            'label_name' => __('Amount'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'days_count',
            'number_value' => isset($payroll_adjustment) ? $payroll_adjustment->days_count : 0,
            'label_name' => __('Days Count'),
            
        ])
    </div>
    <div class="col-md-12">
        @include('admin.layouts.forms.fields.textarea', [
            'text_name' => 'reason',
            'text_value' => isset($payroll_adjustment) ? $payroll_adjustment->reason : null,
            'label_name' => __('Reason'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'status',
            'text_value' => isset($payroll_adjustment) ? $payroll_adjustment->status : null,
            'label_name' => __('Status'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date', [
            'date_name' => 'effective_date',
            'date_value' => isset($payroll_adjustment) ? $payroll_adjustment->effective_date : null,
            'label_name' => __('Effective Date'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Created By'),
            'select_name' => 'created_by',
            'select_value' => isset($payroll_adjustment) ? $payroll_adjustment->created_by : null,
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Approved By'),
            'select_name' => 'approved_by',
            'select_value' => isset($payroll_adjustment) ? $payroll_adjustment->approved_by : null,
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Cancelled By'),
            'select_name' => 'cancelled_by',
            'select_value' => isset($payroll_adjustment) ? $payroll_adjustment->cancelled_by : null,
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'order_id',
            'number_value' => isset($payroll_adjustment) ? $payroll_adjustment->order_id : 0,
            'label_name' => __('Order Id'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.selects.active')
    </div>
</div>
@include('admin.layouts.forms.footer')

