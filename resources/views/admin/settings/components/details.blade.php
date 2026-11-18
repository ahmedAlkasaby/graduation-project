                            <div id="site-details" class="content">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        @include('admin.layouts.forms.fields.text', [
                                            'text_name' => 'site_title',
                                            'text_value' => $setting['site_title'] ?? null,
                                            'label_name' => __('Site Title'),
                                            'label_req' => true,
                                        ])
                                    </div>
                                    <div class="col-md-6">
                                        @include('admin.layouts.forms.fields.text', [
                                            'text_name' => 'site_phone',
                                            'text_value' => $setting['site_phone'] ?? null,
                                            'label_name' => __('Site Phone'),
                                            'label_req' => true,
                                        ])
                                    </div>
                                    <div class="col-md-6">
                                        @include('admin.layouts.forms.fields.text', [
                                            'text_name' => 'site_email',
                                            'text_value' => $setting['site_email'] ?? null,
                                            'label_name' => __('Site Email'),
                                            'label_req' => true,
                                            'text_type' => 'email',
                                        ])
                                    </div>
                                    <div class="col-md-6">
                                        @include('admin.layouts.forms.fields.select', [
                                            'select_name' => 'table_limit',
                                            'select_value' => $setting['table_limit'] ?? null,
                                            'select_function' => [25 => 25, 50 => 50, 100 => 100, 250 => 250, 500 => 500, 1000 => 1000],
                                            'label' => __('Show Result'),
                                        ])
                                    </div>
                                    <div class="col-md-6">
                                        @include('admin.layouts.forms.fields.select', [
                                            'select_name' => 'mobile_limit',
                                            'select_value' => $setting['mobile_limit'] ?? null,
                                            'select_function' => [10 => 10, 15 => 15, 20 => 20, 25 => 25, 50 => 50, 100 => 100],
                                            'label' => __('Mobile Page Size'),
                                        ])
                                    </div>
                                    <div class="col-md-6">
                                        @include('admin.layouts.forms.fields.select', [
                                            'select_name' => 'admin_language',
                                            'select_value' => $setting['admin_language'] ?? null,
                                            'select_function' => languageType(),
                                            'label' => __('Admin Language'),
                                        ])
                                    </div>
                                    <div class="col-md-6">
                                        @include('admin.layouts.forms.fields.select', [
                                            'select_name' => 'site_language',
                                            'select_value' => $setting['site_language'] ?? null,
                                            'select_function' => languageType(),
                                            'label' => __('Site Language'),
                                        ])
                                    </div>
                                    <div class="col-md-6">
                                        @include('admin.layouts.forms.fields.text', [
                                            'text_name' => 'address',
                                            'text_value' => $setting['address'] ?? null,
                                            'label_name' => __('Address'),
                                            'not_req' => true,
                                        ])
                                    </div>
                                    <div class="col-md-6 ">
                                        @include('admin.layouts.forms.fields.select', [
                                            'select_name' => 'site_open',
                                            'select_value' => $setting['site_open'] ?? null,
                                            'select_function' => showType(),
                                            'label' => __('Site Open'),
                                        ])
                                    </div>
                                    <div class="col-md-6 ">
                                        @include('admin.layouts.forms.fields.select', [
                                            'select_name' => 'app_open',
                                            'select_value' => $setting['app_open'] ?? null,
                                            'select_function' => showType(),
                                            'label' => __('App Open'),
                                        ])
                                    </div>

                                    <div class="col-md-12 ">
                                        <label class="form-label">{{ __('Site Logo') }}</label>
                                        @include('admin.layouts.forms.dropzone.dropzone', [
                                            'name' => 'image',
                                            'existingImageUrl' => !empty($image) ? asset($image) : null,
                                            'image_size' => $imagesize ?? null,
                                        ])
                                    </div>

                                    <!-- Developer Tools Section -->
                                    <div class="col-12 mt-4">
                                        <hr class="my-4">
                                        <h5 class="mb-3">{{ __('Developer Tools') }}</h5>
                                        <div class="alert alert-info" role="alert">
                                            <i class="ti ti-info-circle me-2"></i>
                                            {{ __('These tools help optimize application performance and clear cached data.') }}
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6 class="card-title">
                                                            <i class="ti ti-bolt text-warning me-2"></i>
                                                            {{ __('Optimize Application') }}
                                                        </h6>
                                                        <p class="card-text small text-muted">
                                                            {{ __('Cache configuration, routes, views, and events for better performance.') }}
                                                        </p>
                                                        <a href="{{ route('admin.settings.optimize') }}"
                                                           class="btn btn-warning"
                                                           onclick="return confirm('{{ __('Are you sure you want to optimize the application?') }}')">
                                                            <i class="ti ti-rocket me-1"></i>
                                                            {{ __('Optimize') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6 class="card-title">
                                                            <i class="ti ti-trash text-danger me-2"></i>
                                                            {{ __('Clear Optimization Cache') }}
                                                        </h6>
                                                        <p class="card-text small text-muted">
                                                            {{ __('Remove all cached files. Use after code changes or debugging.') }}
                                                        </p>
                                                        <a href="{{ route('admin.settings.optimize.clear') }}"
                                                           class="btn btn-danger"
                                                           onclick="return confirm('{{ __('Are you sure you want to clear all cached data?') }}')">
                                                            <i class="ti ti-eraser me-1"></i>
                                                            {{ __('Clear Cache') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
