@include('admin.layouts.forms.head', [
    'show_name' => true,
])
<div class="row g-3 mt-2 mb-4">
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Employee Id'),
            'select_name' => 'employee_id',
            'select_value' => isset($payroll) ? $payroll->employee_id : null,
            'select_function' => $employees ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'month',
            'number_value' => isset($payroll) ? $payroll->month : 0,
            'label_name' => __('Month'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'year',
            'number_value' => isset($payroll) ? $payroll->year : 0,
            'label_name' => __('Year'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date', [
            'date_name' => 'period_start',
            'date_value' => isset($payroll) ? $payroll->period_start : null,
            'label_name' => __('Period Start'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date', [
            'date_name' => 'period_end',
            'date_value' => isset($payroll) ? $payroll->period_end : null,
            'label_name' => __('Period End'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'basic_salary',
            'number_value' => isset($payroll) ? $payroll->basic_salary : 0,
            'label_name' => __('Basic Salary'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'present_days',
            'number_value' => isset($payroll) ? $payroll->present_days : 0,
            'label_name' => __('Present Days'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'absent_days',
            'number_value' => isset($payroll) ? $payroll->absent_days : 0,
            'label_name' => __('Absent Days'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'holiday_days',
            'number_value' => isset($payroll) ? $payroll->holiday_days : 0,
            'label_name' => __('Holiday Days'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'paid_holiday_days',
            'number_value' => isset($payroll) ? $payroll->paid_holiday_days : 0,
            'label_name' => __('Paid Holiday Days'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'unpaid_leave_days',
            'number_value' => isset($payroll) ? $payroll->unpaid_leave_days : 0,
            'label_name' => __('Unpaid Leave Days'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'total_late_minutes',
            'number_value' => isset($payroll) ? $payroll->total_late_minutes : 0,
            'label_name' => __('Total Late Minutes'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'total_overtime_minutes',
            'number_value' => isset($payroll) ? $payroll->total_overtime_minutes : 0,
            'label_name' => __('Total Overtime Minutes'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'total_additions',
            'number_value' => isset($payroll) ? $payroll->total_additions : 0,
            'label_name' => __('Total Additions'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'total_deductions',
            'number_value' => isset($payroll) ? $payroll->total_deductions : 0,
            'label_name' => __('Total Deductions'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'net_salary',
            'number_value' => isset($payroll) ? $payroll->net_salary : 0,
            'label_name' => __('Net Salary'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'status',
            'text_value' => isset($payroll) ? $payroll->status : null,
            'label_name' => __('Status'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date-time', [
            'date_name' => 'processed_at',
            'date_value' => isset($payroll) ? $payroll->processed_at : null,
            'label_name' => __('Processed At'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date-time', [
            'date_name' => 'paid_at',
            'date_value' => isset($payroll) ? $payroll->paid_at : null,
            'label_name' => __('Paid At'),
            
        ])
    </div>
    <div class="col-md-12">
        @include('admin.layouts.forms.fields.textarea', [
            'text_name' => 'notes',
            'text_value' => isset($payroll) ? $payroll->notes : null,
            'label_name' => __('Notes'),
            'not_req' => true,
        ])
    </div>
</div>
@include('admin.layouts.forms.footer')

