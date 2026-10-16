@include('admin.layouts.components.filter.header', [
    'filterKeys' => [
        'user_id', 'model', 'field_id', 'key',
        'date_start', 'date_end', 'platform', 'browser', 'limit',
    ],
])

    <div class="col-sm-4">
        <div class="box">
            <div class="box-body">
                @include('admin.layouts.forms.fields.select', [
                    'not_req' => true,
                    'is_multiple' => true,
                    'label' => __('User'),
                    'select_name' => 'user_id',
                    'select_value' => request()->input('user_id') ?? null,
                    'select_function' => $users,
                ])
                @include('admin.layouts.forms.fields.select', [
                    'not_req' => true,
                    'label' => __('Model'),
                    'select_name' => 'model',
                    'select_value' => request()->input('model') ?? null,
                    'select_function' => $models,
                ])
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="box">
            <div class="box-body">
                @include('admin.layouts.forms.fields.text', [
                    'not_req' => true,
                    'label_name' => __('Resource ID'),
                    'text_name' => 'field_id',
                    'text_value' => request()->input('field_id') ?? null,
                ])
                @include('admin.layouts.forms.fields.text', [
                    'not_req' => true,
                    'label_name' => __('Field'),
                    'text_name' => 'key',
                    'text_value' => request()->input('key') ?? null,
                    'placeholder' => 'name, price, active',
                ])
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="box">
            <div class="box-body">
                @include('admin.layouts.forms.fields.date', [
                    'not_req' => true,
                    'label_name' => __('Date From'),
                    'date_name' => 'date_start',
                    'date_value' => request()->input('date_start') ?? null,
                ])
                @include('admin.layouts.forms.fields.date', [
                    'not_req' => true,
                    'label_name' => __('Date To'),
                    'date_name' => 'date_end',
                    'date_value' => request()->input('date_end') ?? null,
                ])
                @include('admin.layouts.forms.fields.text', [
                    'not_req' => true,
                    'label_name' => __('Platform'),
                    'text_name' => 'platform',
                    'text_value' => request()->input('platform') ?? null,
                    'placeholder' => 'Windows, macOS, Android',
                ])
                @include('admin.layouts.forms.fields.text', [
                    'not_req' => true,
                    'label_name' => __('Browser'),
                    'text_name' => 'browser',
                    'text_value' => request()->input('browser') ?? null,
                    'placeholder' => 'Chrome, Safari, Firefox',
                ])
            </div>
        </div>
    </div>

@include('admin.layouts.components.filter.footer')
