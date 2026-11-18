@include('admin.layouts.components.filter.header', [
    'filterKeys' => ['search', 'active', 'limit'],
])

    <div class="col-sm-4">
        <div class="box">
            <div class="box-body">
                @include('admin.layouts.forms.fields.text', [
                    'not_req' => true,
                    'text_name' => 'search',
                    'text_value' => request()->input('search') ?? null,
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
                    'select_value' => request()->input('active') ?? null,
                    'select_function' => ['' => __('All'), '1' => __('Active'), '0' => __('Inactive')],
                ])
            </div>
        </div>
    </div>

@include('admin.layouts.components.filter.footer')
