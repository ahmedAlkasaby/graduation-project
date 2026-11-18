<div class="card role-card role-permissions">
    <div class="card-header border-bottom pb-0">
        <ul class="nav nav-pills mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#role-tab-resources" role="tab">
                    {{ __('Resources') }}
                    <span class="badge rounded-pill bg-label-warning ms-1">{{ count($data['resources']) }}</span>
                </button>
            </li>
            @if (! empty($data['custom']))
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#role-tab-custom" role="tab">
                        {{ __('Custom Permissions') }}
                        <span class="badge rounded-pill bg-label-warning ms-1">{{ count($data['custom']) }}</span>
                    </button>
                </li>
            @endif
        </ul>
    </div>

    <div class="card-body pt-4">
        <div class="tab-content p-0">
            <div class="tab-pane fade show active" id="role-tab-resources" role="tabpanel">
                @foreach ($data['resources'] as $resource)
                    <div class="card border shadow-none mb-4 role-permission-group">
                        <div class="card-header d-flex justify-content-between align-items-start cursor-pointer"
                            data-bs-toggle="collapse" data-bs-target="#role-resource-{{ $resource['key'] }}" aria-expanded="true">
                            <div>
                                <h6 class="mb-1">{{ __($resource['category_name']) }}</h6>
                                <small class="text-muted">{{ $resource['key'] }}</small>
                            </div>
                            <i class="ti ti-chevron-up role-collapse-icon"></i>
                        </div>
                        <div class="collapse show" id="role-resource-{{ $resource['key'] }}">
                            <div class="card-body border-top pt-3">
                                <a href="javascript:void(0)" class="role-group-toggle d-inline-block fw-medium mb-3"
                                    data-select="{{ __('Select all') }}" data-deselect="{{ __('Deselect all') }}">{{ __('Select all') }}</a>
                                <div class="row g-3">
                                    @foreach ($resource['permissions'] as $id => $permission)
                                        <div class="col-6 col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input role-permission" type="checkbox" name="permissions[]"
                                                    id="permission-{{ $id }}" value="{{ $id }}" @checked(in_array($id, $rolePermissions))>
                                                <label class="form-check-label" for="permission-{{ $id }}">{{ __(\Illuminate\Support\Str::headline($permission)) }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if (! empty($data['custom']))
                <div class="tab-pane fade role-permission-group" id="role-tab-custom" role="tabpanel">
                    <label class="form-label fw-medium" for="role-custom-search">{{ __('Custom permissions') }}</label>
                    <div class="input-group input-group-merge mb-3">
                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                        <input type="search" class="form-control" id="role-custom-search" placeholder="{{ __('Start typing to search...') }}">
                    </div>
                    <a href="javascript:void(0)" class="role-group-toggle d-inline-block fw-medium mb-3"
                        data-select="{{ __('Select all') }}" data-deselect="{{ __('Deselect all') }}">{{ __('Select all') }}</a>
                    <div class="row g-3">
                        @foreach ($data['custom'] as $permission)
                            <div class="col-12 col-md-6 col-xl-3 role-custom-item" data-search="{{ strtolower($permission['label'].' '.$permission['name']) }}">
                                <div class="form-check">
                                    <input class="form-check-input role-permission" type="checkbox" name="permissions[]"
                                        id="permission-{{ $permission['id'] }}" value="{{ $permission['id'] }}" @checked(in_array($permission['id'], $rolePermissions))>
                                    <label class="form-check-label" for="permission-{{ $permission['id'] }}">{{ $permission['label'] }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    @include('admin.layouts.forms.buttons.form-save', ['btn_block' => 'md', 'save' => __('Save changes')])
    <a href="{{ route('admin.roles.index') }}" class="btn btn-label-secondary waves-effect">{{ __('Cancel') }}</a>
</div>

<style>
    .role-card { border-radius: 1rem; }
    .role-permissions .nav-pills .nav-link { color: var(--bs-body-color); }
    .role-permissions .nav-pills .nav-link.active { color: var(--bs-primary); background: rgba(var(--bs-primary-rgb), .08); box-shadow: none; }
    .role-permission-group > .card-header { padding-block: 1rem; }
    .role-permission-group > .card-header[aria-expanded="false"] .role-collapse-icon { transform: rotate(180deg); }
    .role-collapse-icon { transition: transform .2s ease; }
    .role-group-toggle { color: var(--bs-primary); }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('role-select-all');
        const permissions = Array.from(document.querySelectorAll('.role-permission'));
        const groups = Array.from(document.querySelectorAll('.role-permission-group'));

        const groupCheckboxes = (group) => Array.from(group.querySelectorAll('.role-permission'))
            .filter((checkbox) => !checkbox.closest('.role-custom-item.d-none'));

        const refresh = () => {
            groups.forEach((group) => {
                const toggle = group.querySelector('.role-group-toggle');
                if (!toggle) {
                    return;
                }
                const checkboxes = groupCheckboxes(group);
                const allChecked = checkboxes.length > 0 && checkboxes.every((checkbox) => checkbox.checked);
                toggle.textContent = allChecked ? toggle.dataset.deselect : toggle.dataset.select;
            });
            if (selectAll) {
                selectAll.checked = permissions.length > 0 && permissions.every((checkbox) => checkbox.checked);
            }
        };

        selectAll?.addEventListener('change', () => {
            permissions.forEach((checkbox) => checkbox.checked = selectAll.checked);
            refresh();
        });

        groups.forEach((group) => {
            group.querySelector('.role-group-toggle')?.addEventListener('click', () => {
                const checkboxes = groupCheckboxes(group);
                const check = !checkboxes.every((checkbox) => checkbox.checked);
                checkboxes.forEach((checkbox) => checkbox.checked = check);
                refresh();
            });
        });

        permissions.forEach((checkbox) => checkbox.addEventListener('change', refresh));

        document.getElementById('role-custom-search')?.addEventListener('input', (event) => {
            const term = event.target.value.trim().toLowerCase();
            document.querySelectorAll('.role-custom-item').forEach((item) => {
                item.classList.toggle('d-none', term !== '' && !item.dataset.search.includes(term));
            });
            refresh();
        });

        refresh();
    });
</script>
