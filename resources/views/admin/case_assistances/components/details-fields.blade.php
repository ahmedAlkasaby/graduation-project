{{-- The descriptive fields, order and status: the only ones an assistance keeps editable. --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Details') }}</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-sm-6">
                @include('admin.layouts.forms.fields.text', [
                    'label_req' => true,
                    'label_name' => __('English Title'),
                    'text_name' => 'title[en]',
                    'text_value' => isset($assistance) ? $assistance->titleLang('en') : null,
                ])
            </div>
            <div class="col-sm-6">
                @include('admin.layouts.forms.fields.text', [
                    'label_req' => true,
                    'label_name' => __('Arabic Title'),
                    'text_name' => 'title[ar]',
                    'text_value' => isset($assistance) ? $assistance->titleLang('ar') : null,
                ])
            </div>
            <div class="col-12">
                @include('admin.layouts.forms.fields.textarea', [
                    'not_req' => true,
                    'label_name' => __('Description'),
                    'text_name' => 'description',
                    'text_value' => $assistance->description ?? null,
                    'rows' => 3,
                ])
            </div>
            <div class="col-sm-6">
                @include('admin.layouts.forms.fields.number', [
                    'not_req' => true,
                    'label' => __('Order ID'),
                    'number_name' => 'order_id',
                    'number_value' => $assistance->order_id ?? null,
                    'min' => 1,
                    'max' => 127,
                ])
            </div>
            <div class="col-sm-6">
                @include('admin.layouts.forms.fields.select', [
                    'label_req' => true,
                    'label_name' => __('Status'),
                    'select_name' => 'active',
                    'select_value' => $assistance->active ?? 1,
                ])
            </div>
        </div>
    </div>
</div>
