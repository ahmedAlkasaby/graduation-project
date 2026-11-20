@include('admin.layouts.components.filter.header', [
    'filterKeys' => ['search', 'active', 'limit'],
])

    <div class="col-sm-6">
        <div class="box">
            <div class="box-body">
                @include('admin.layouts.forms.fields.text', [
                    'not_req' => true,
                    'label_name' => __('Search'),
                    'text_name' => 'search',
                    'text_value' => request()->input('search'),
                ])
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="box">
            <div class="box-body">
                @include('admin.layouts.forms.fields.select', [
                    'not_req' => true,
                    'label' => __('Status'),
                    'select_name' => 'active',
                    'select_value' => request()->input('active'),
                    'select_function' => ['' => __('All'), '1' => __('Active'), '0' => __('Inactive')],
                ])
            </div>
        </div>
    </div>

@include('admin.layouts.components.filter.footer')
