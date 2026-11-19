@include('admin.layouts.components.filter.header', [
    'filterKeys' => ['search', 'type', 'active', 'trashed', 'date_start', 'date_end', 'limit'],
])

    <div class="col-sm-4">
        <div class="box">
            <div class="box-body">
                @include('admin.layouts.forms.fields.text', [
                    'not_req' => true,
                    'label_name' => __('Search'),
                    'placeholder' => __('Name, email or phone'),
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
                    'select_name' => 'type',
                    'select_value' => request()->input('type'),
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
                    'label' => __('Status'),
                    'select_name' => 'active',
                    'select_value' => request()->input('active'),
                    'select_function' => ['' => __('All'), '1' => __('Active'), '0' => __('Inactive')],
                ])
            </div>
        </div>
    </div>

    @can('users.restore')
        <div class="col-sm-4">
            <div class="box">
                <div class="box-body">
                    @include('admin.layouts.forms.fields.select', [
                        'not_req' => true,
                        'label' => __('Deleted'),
                        'select_name' => 'trashed',
                        'select_value' => request()->input('trashed'),
                        'select_function' => $trashedOptions,
                    ])
                </div>
            </div>
        </div>
    @endcan

    <div class="col-sm-4">
        <div class="box">
            <div class="box-body">
                @include('admin.layouts.forms.fields.date', [
                    'not_req' => true,
                    'label_name' => __('Date Start'),
                    'date_name' => 'date_start',
                    'date_value' => request()->input('date_start'),
                ])
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="box">
            <div class="box-body">
                @include('admin.layouts.forms.fields.date', [
                    'not_req' => true,
                    'label_name' => __('Date End'),
                    'date_name' => 'date_end',
                    'date_value' => request()->input('date_end'),
                ])
            </div>
        </div>
    </div>

@include('admin.layouts.components.filter.footer')
