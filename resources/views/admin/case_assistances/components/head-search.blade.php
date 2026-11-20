@include('admin.layouts.components.filter.header', [
    'filterKeys' => ['search', 'category_id', 'assistance_type', 'case_id'],
])

    <div class="col-sm-4">
        <div class="box">
            <div class="box-body">
                @include('admin.layouts.forms.fields.text', [
                    'not_req' => true,
                    'label_name' => __('Search by title, beneficiary, national ID or phone'),
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
                    'label' => __('Type'),
                    'select_name' => 'assistance_type',
                    'select_value' => request()->input('assistance_type'),
                    'select_function' => $typeOptions,
                ])
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="box">
            <div class="box-body">
                @include('admin.layouts.forms.fields.select', [
                    'not_req' => true,
                    'label' => __('Category'),
                    'select_name' => 'category_id',
                    'select_value' => request()->input('category_id'),
                    'select_function' => $categories,
                ])
            </div>
        </div>
    </div>

@include('admin.layouts.components.filter.footer', ['limit_filter' => true])
