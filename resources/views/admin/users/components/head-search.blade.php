@include('admin.layouts.components.filter.header', [
    'filterKeys' => ['name', 'email', 'phone', 'type', 'active', 'date_start', 'date_end', 'limit'],
])

    <div class="col-sm-4">
        <div class="box">
            <div class="box-body">
                @include('admin.layouts.forms.fields.text', [
                    'not_req' => true,
                    'text_name' => 'name',
                    'text_value' => request()->input('name') ?? null,
                ])
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="box">
            <div class="box-body">
                @include('admin.layouts.forms.fields.text', [
                    'not_req' => true,
                    'text_name' => 'email',
                    'text_type' => 'email',
                    'text_value' => request()->input('email') ?? null,
                ])
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="box">
            <div class="box-body">
                @include('admin.layouts.forms.fields.text', [
                    'not_req' => true,
                    'text_name' => 'phone',
                    'text_value' => request()->input('phone') ?? null,
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
                    'select_value' => request()->input('type') ?? null,
                    'select_function' => userTypeDefault(),
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

    <div class="col-sm-4">
        <div class="box">
            <div class="box-body">
                @include('admin.layouts.forms.fields.date', [
                    'not_req' => true,
                    'label' => __('Date Start'),
                    'date_name' => 'date_start',
                    'date_value' => request()->input('date_start') ?? null,
                ])
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="box">
            <div class="box-body">
                @include('admin.layouts.forms.fields.date', [
                    'not_req' => true,
                    'label' => __('Date End'),
                    'date_name' => 'date_end',
                    'date_value' => request()->input('date_end') ?? null,
                ])
            </div>
        </div>
    </div>

@include('admin.layouts.components.filter.footer')
