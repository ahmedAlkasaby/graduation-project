@include('admin.layouts.forms.head', [
    'show_name' => true,
])
<div class="row g-3 mt-2 mb-4">
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date', [
            'date_name' => 'date_start',
            'date_value' => isset($holiday) ? $holiday->date_start?->toDateString() : null,
            'label_name' => __('Start Date'),
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date', [
            'date_name' => 'date_end',
            'date_value' => isset($holiday) ? $holiday->date_end?->toDateString() : null,
            'label_name' => __('End Date'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'order_id',
            'number_value' => isset($holiday) ? $holiday->order_id : 0,
            'label_name' => __('Order Id'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.selects.active')
    </div>
</div>
@include('admin.layouts.forms.footer')

