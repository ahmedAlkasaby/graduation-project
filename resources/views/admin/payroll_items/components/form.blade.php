@include('admin.layouts.forms.head', [
    'show_name' => false,
])
<div class="row g-3 mt-2 mb-4">
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Payroll Id'),
            'select_name' => 'payroll_id',
            'select_value' => isset($payroll_item) ? $payroll_item->payroll_id : null,
            'select_function' => $payrolls ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'type',
            'text_value' => isset($payroll_item) ? $payroll_item->type : null,
            'label_name' => __('Type'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'category',
            'text_value' => isset($payroll_item) ? $payroll_item->category : null,
            'label_name' => __('Category'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'amount',
            'number_value' => isset($payroll_item) ? $payroll_item->amount : 0,
            'label_name' => __('Amount'),
            
        ])
    </div>
</div>
@include('admin.layouts.forms.footer')

