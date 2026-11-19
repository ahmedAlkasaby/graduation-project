<div class="col-12 mb-4">
    <div class="card role-card mb-4">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="row g-3">
                        <div class="col-md-6">
                            @include('admin.layouts.forms.fields.text', [
                                'label_req' => true,
                                'label_name' => __('Name'),
                                'text_name' => 'name',
                                'placeholder' => 'e.g. city_manager',
                            ] + ($isSystemRole ? ['read_only' => true] : []))
                            <small class="text-muted">{{ $isSystemRole ? __('A seeded role keeps its name') : __('Lowercase letters, numbers and underscores only') }}</small>
                        </div>
                        <div class="col-md-6">
                            @include('admin.layouts.forms.fields.text', [
                                'label_req' => true,
                                'label_name' => __('Display Name'),
                                'text_name' => 'display_name',
                            ])
                        </div>
                        <div class="col-12">
                            @include('admin.layouts.forms.fields.textarea', [
                                'not_req' => true,
                                'label_name' => __('Description'),
                                'text_name' => 'description',
                            ])
                        </div>
                        <div class="col-12">
                            <label class="form-label d-block">{{ __('Users with this role can sign in on') }}</label>
                            @foreach (['can_login_web' => __('Web dashboard'), 'can_login_mobile' => __('Mobile app')] as $platformField => $platformLabel)
                                <input type="hidden" name="{{ $platformField }}" value="0">
                                <label class="switch switch-success me-4">
                                    <input type="checkbox" class="switch-input" name="{{ $platformField }}" value="1" @checked(old($platformField, isset($role) ? $role->{$platformField} : true))>
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"><i class="ti ti-check"></i></span>
                                        <span class="switch-off"><i class="ti ti-x"></i></span>
                                    </span>
                                    <span class="switch-label">{{ $platformLabel }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <label class="switch switch-primary mt-lg-4">
                        <input type="checkbox" class="switch-input" id="role-select-all">
                        <span class="switch-toggle-slider">
                            <span class="switch-on"><i class="ti ti-shield-check"></i></span>
                            <span class="switch-off"><i class="ti ti-shield"></i></span>
                        </span>
                        <span class="switch-label fw-medium">{{ __('Select All') }}</span>
                    </label>
                    <small class="d-block text-muted mt-1">{{ __('Enables/Disables all Permissions for this role') }}</small>
                </div>
            </div>
        </div>
    </div>

    @include('admin.roles.components.permissions-table', ['data' => $permissions])
</div>
