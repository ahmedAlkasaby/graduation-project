@include('admin.layouts.forms.head', ['show_name' => true])
<div class="content-header mb-3"></div>
@include('admin.layouts.forms.models.country')
<div class="content-header mb-3"></div>
@include('admin.layouts.forms.fields.number', [
    'min' => 1,
    'max' => 127,
    'number_name' => 'order_id',
    'label_name' => __('Order ID'),
    'not_req' => true,
])
<div class="content-header mb-3"></div>
@include('admin.layouts.forms.texts.latitude')
<div class="content-header mb-3"></div>
@include('admin.layouts.forms.texts.longitude')
<div class="content-header mb-3"></div>
@include('admin.layouts.forms.fields.select', [
    'select_name' => 'active',
    'label_name' => __('Status'),
])
<div class="content-header mb-3"></div>
@include('admin.layouts.forms.footer')
