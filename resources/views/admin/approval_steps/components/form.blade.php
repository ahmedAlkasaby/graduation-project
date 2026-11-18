@include('admin.layouts.forms.head')
<div class="row g-3 mt-2 mb-4">
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'request_type',
            'text_value' => isset($approval_step) ? $approval_step->request_type : null,
            'label_name' => __('Request Type'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Request Id'),
            'select_name' => 'request_id',
            'select_value' => isset($approval_step) ? $approval_step->request_id : null,
            'select_function' => $requests ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'step_order',
            'number_value' => isset($approval_step) ? $approval_step->step_order : 0,
            'label_name' => __('Step Order'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Approver Id'),
            'select_name' => 'approver_id',
            'select_value' => isset($approval_step) ? $approval_step->approver_id : null,
            'select_function' => $approvers ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'approver_role',
            'text_value' => isset($approval_step) ? $approval_step->approver_role : null,
            'label_name' => __('Approver Role'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'status',
            'text_value' => isset($approval_step) ? $approval_step->status : null,
            'label_name' => __('Status'),
            
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date-time', [
            'date_name' => 'acted_at',
            'date_value' => isset($approval_step) ? $approval_step->acted_at : null,
            'label_name' => __('Acted At'),
            
        ])
    </div>
    <div class="col-md-12">
        @include('admin.layouts.forms.fields.textarea', [
            'text_name' => 'note',
            'text_value' => isset($approval_step) ? $approval_step->note : null,
            'label_name' => __('Note'),
            'not_req' => true,
        ])
    </div>
</div>
@include('admin.layouts.forms.footer')

