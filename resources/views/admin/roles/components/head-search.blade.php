@include('admin.layouts.components.filter.header', [
    'filterKeys' => ['search', 'limit'],
])

    <div class="col-sm-6">
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

@include('admin.layouts.components.filter.footer')
