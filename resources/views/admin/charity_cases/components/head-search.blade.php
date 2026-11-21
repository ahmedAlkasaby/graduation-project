@include('admin.layouts.components.filter.header', [
    'filterKeys' => ['search', 'volunteer_id', 'limit'],
])

    <div class="col-sm-6">
        <div class="box">
            <div class="box-body">
                @include('admin.layouts.forms.fields.text', [
                    'not_req' => true,
                    'label_name' => __('Search by name, national ID or phone'),
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
                    'label' => __('Volunteer'),
                    'select_name' => 'volunteer_id',
                    'select_value' => request()->input('volunteer_id'),
                    'select_function' => $volunteers,
                ])
            </div>
        </div>
    </div>

@include('admin.layouts.components.filter.footer')
