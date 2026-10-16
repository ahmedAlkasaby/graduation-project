{{--
    Closes the panel opened by filter.header, adding the per-page select and
    the search / reset buttons. Pass $limit_filter to drop the per-page control.
--}}
                            <div class="col-sm-4">
                                <div class="box">
                                    <div class="box-body">
                                        @if (!isset($limit_filter))
                                            @include('admin.layouts.forms.fields.select', [
                                                'not_req' => true,
                                                'label' => __('Per Page'),
                                                'select_name' => 'limit',
                                                'select_value' => request()->input('limit') ?? null,
                                                'select_function' => perPageOptions('yes'),
                                            ])
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="content-header mb-3"></div>
                        <div class="col-12 text-center d-flex justify-content-center gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-filter me-1"></i>{{ __('Search') }}
                            </button>
                            <a href="{{ url()->current() }}" class="btn btn-outline-secondary ms-2">
                                <i class="ti ti-x me-1"></i>{{ __('Reset') }}
                            </a>
                        </div>
                        @include('admin.layouts.forms.close')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
