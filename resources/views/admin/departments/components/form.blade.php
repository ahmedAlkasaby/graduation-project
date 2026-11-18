@include('admin.layouts.forms.head', [
    'show_name' => true,
    'not_req'   => false,
])
<div class="row g-3 mt-2 mb-4">
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'order_id',
            'number_value' => isset($department) ? $department->order_id : 0,
            'label_name' => __('Order Id'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.selects.active')
    </div>
</div>
@include('admin.layouts.forms.footer')
