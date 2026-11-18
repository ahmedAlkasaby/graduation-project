<div class="col-12 mb-4">
    <div class="card role-card mb-4">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="row g-3">
                        <div class="col-md-6">
                            @include('admin.layouts.forms.fields.select', [
                                'label_name' => __('Name'),
                                'select_name' => 'name',
                                'select_function' => $roleNameOptions,
                            ])
                        </div>
                        <div class="col-md-6">
                            @include('admin.layouts.forms.texts.name', [
                                'name' => __('Display Name'),
                                'name_text' => 'display_name',
                                'not_req' => true,
                            ])
                        </div>
                        <div class="col-12">
                            @include('admin.layouts.forms.fields.textarea', [
                                'text_name' => 'description',
                                'label_name' => __('Description'),
                                'not_req' => true,
                            ])
                        </div>
                        <div class="col-12">
                            <label class="form-label d-block">{{ __('Users with this role can sign in on') }}</label>
                            @foreach (['can_login_web' => [__('Web dashboard'), false], 'can_login_mobile' => [__('Mobile app'), true]] as $platformField => [$platformLabel, $platformDefault])
                                <input type="hidden" name="{{ $platformField }}" value="0">
                                <label class="switch switch-success me-4">
                                    <input type="checkbox" class="switch-input" name="{{ $platformField }}" value="1" @checked(old($platformField, isset($role) ? $role->{$platformField} : $platformDefault))>
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
