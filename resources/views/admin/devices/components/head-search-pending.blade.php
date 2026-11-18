@include('admin.layouts.components.filter.header', [
    'filterKeys' => ['search', 'platform', 'user_id', 'limit'],
])

                                <div class="col-sm-4">
                                    <div class="box">
                                        <div class="box-body">
                                            @include('admin.layouts.forms.fields.text', [
                                                'label_name' => __('Search'),
                                                'not_req' => true,
                                                'text_name' => 'search',
                                                'text_value' => request()->input('search') ?? null,
                                                'placeholder' => __('Device name or installation ID'),
                                            ])
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="box">
                                        <div class="box-body">
                                            @include('admin.layouts.forms.fields.select', [
                                                'not_req' => true,
                                                'label' => __('Platform'),
                                                'select_name' => 'platform',
                                                'select_value' => request()->input('platform') ?? null,
                                                'select_function' => [
                                                    '' => __('All Platforms'),
                                                    'android' => __('Android'),
                                                    'ios' => __('iOS'),
                                                    'web' => __('Web'),
                                                ],
                                            ])
                                        </div>
                                    </div>
                                </div>

@include('admin.layouts.components.filter.footer')
