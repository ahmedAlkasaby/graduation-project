@include('admin.layouts.components.filter.header', [
    'filterKeys' => ['search', 'state', 'type', 'limit'],
])

                                <div class="col-sm-4">
                                    <div class="box">
                                        <div class="box-body">
                                            @include('admin.layouts.forms.fields.text', [
                                                'label_name' => __('Search'),
                                                'not_req' => true,
                                                'text_name' => 'search',
                                                'text_value' => request()->input('search') ?? null,
                                                'placeholder' => __('IMEI or device name'),
                                            ])
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="box">
                                        <div class="box-body">
                                            @include('admin.layouts.forms.fields.select', [
                                                'not_req' => true,
                                                'label' => __('State'),
                                                'select_name' => 'state',
                                                'select_value' => request()->input('state') ?? null,
                                                'select_function' => \App\Enums\DeviceStateEnum::optionsWithPlaceholder(),
                                            ])
                                        </div>
                                    </div>
                                </div>

@include('admin.layouts.components.filter.footer')
