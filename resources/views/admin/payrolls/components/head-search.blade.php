@include('admin.layouts.components.filter.header', [
    'filterKeys' => ['search', 'year', 'month', 'status', 'limit'],
])

                                <div class="col-sm-4">
                                    <div class="box">
                                        <div class="box-body">
                                            @include('admin.layouts.forms.fields.text', [
                                                'label_name' => __('Search (Name)'),
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
                                                'label_name' => __('Year'),
                                                'select_name' => 'year',
                                                'select_value' => request()->input('year') ?? null,
                                                'select_function' => ['' => __('All')] +
                                                    collect(range(now()->year, now()->year - 5))
                                                        ->mapWithKeys(fn ($y) => [$y => $y])
                                                        ->all(),
                                            ])
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="box">
                                        <div class="box-body">
                                            @include('admin.layouts.forms.fields.select', [
                                                'not_req' => true,
                                                'label_name' => __('Month'),
                                                'select_name' => 'month',
                                                'select_value' => request()->input('month') ?? null,
                                                'select_function' => ['' => __('All')] +
                                                    collect(range(1, 12))
                                                        ->mapWithKeys(fn ($m) => [
                                                            $m => \Illuminate\Support\Carbon::create(null, $m, 1)->translatedFormat('F'),
                                                        ])
                                                        ->all(),
                                            ])
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="box">
                                        <div class="box-body">
                                            @include('admin.layouts.forms.fields.select', [
                                                'not_req' => true,
                                                'label_name' => __('Payroll Status'),
                                                'select_name' => 'status',
                                                'select_value' => request()->input('status') ?? null,
                                                'select_function' => ['' => __('All')] +
                                                    \App\Enums\PayrollStatusEnum::options(),
                                            ])
                                        </div>
                                    </div>
                                </div>


@include('admin.layouts.components.filter.footer')
