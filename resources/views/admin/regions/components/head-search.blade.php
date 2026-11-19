@include('admin.layouts.components.filter.header', [
    'filterKeys' => ['search', 'city_id', 'active', 'limit'],
])

    <div class="col-sm-4">
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

    <div class="col-sm-4">
        <div class="box">
            <div class="box-body">
                @include('admin.layouts.forms.fields.select', [
                    'not_req' => true,
                    'label' => __('City'),
                    'select_name' => 'city_id',
                    'select_value' => request()->input('city_id'),
                    'select_function' => $cities,
                ])
            </div>
        </div>
    </div>

    <div class="col-sm-4">
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
