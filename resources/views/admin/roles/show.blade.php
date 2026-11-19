@extends('admin.layouts.app')
@section('title') {{ __('Show Role') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => route('admin.roles.index')])
@stop
@section('content')
    {{-- Role header --}}
    <div class="card role-show-hero mb-4">
        <div class="card-body d-flex flex-column flex-md-row align-items-md-center gap-4">
            <span class="avatar avatar-xl flex-shrink-0">
                <span class="avatar-initial rounded-3 bg-label-primary">
                    <i class="ti ti-shield-lock ti-lg"></i>
                </span>
            </span>
            <div class="flex-grow-1">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <h4 class="mb-0">{{ $role->label() }}</h4>
                    @if ($isSystemRole)
                        <span class="badge bg-label-warning"><i class="ti ti-lock ti-xs me-1"></i>{{ __('System role') }}</span>
                    @else
                        <span class="badge bg-label-info">{{ __('Custom role') }}</span>
                    @endif
                </div>
                <code class="d-inline-block mb-2">{{ $role->name }}</code>
                <p class="text-muted mb-0">{{ $role->description ?: __('No description') }}</p>
            </div>
            @if ($canEdit)
                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary waves-effect flex-shrink-0">
                    <i class="ti ti-edit me-1"></i>{{ __('Edit Role') }}
                </a>
            @endif
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-4 mb-4">
        @foreach ([
            ['label' => __('Users'), 'value' => number_format($role->users_count), 'icon' => 'users', 'color' => 'info'],
            ['label' => __('Permissions granted'), 'value' => $grantedPermissions.' / '.$totalPermissions, 'icon' => 'key', 'color' => 'primary'],
            ['label' => __('Modules covered'), 'value' => $coveredModules.' / '.$modules->count(), 'icon' => 'layout-grid', 'color' => 'warning'],
            ['label' => __('Coverage'), 'value' => $coveragePercent.'%', 'icon' => 'chart-pie', 'color' => 'success'],
        ] as $stat)
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-heading">{{ $stat['label'] }}</span>
                            <h4 class="mb-0 mt-2">{{ $stat['value'] }}</h4>
                        </div>
                        <span class="avatar">
                            <span class="avatar-initial rounded bg-label-{{ $stat['color'] }}">
                                <i class="ti ti-{{ $stat['icon'] }} ti-md"></i>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        {{-- Permissions by module --}}
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="card-title mb-1">{{ __('Permissions') }}</h5>
                        <small class="text-muted">{{ __('What this role can do, module by module') }}</small>
                    </div>
                    <label class="switch switch-primary mb-0">
                        <input type="checkbox" class="switch-input" id="role-show-granted-only">
                        <span class="switch-toggle-slider">
                            <span class="switch-on"><i class="ti ti-check"></i></span>
                            <span class="switch-off"><i class="ti ti-x"></i></span>
                        </span>
                        <span class="switch-label">{{ __('Show granted only') }}</span>
                    </label>
                </div>
                <div class="card-body">
                    @if ($modules->isEmpty())
                        <p class="text-muted mb-0">{{ __('No permissions') }}</p>
                    @else
                        <div class="row g-3">
                            @foreach ($modules as $module)
                                <div class="col-md-6 role-show-module" data-granted="{{ $module['granted'] }}">
                                    <div class="border rounded-3 p-3 h-100">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-0">{{ $module['name'] }}</h6>
                                                <small class="text-muted">{{ $module['key'] }}</small>
                                            </div>
                                            <span class="badge rounded-pill {{ $module['granted'] === $module['total'] ? 'bg-label-success' : ($module['granted'] > 0 ? 'bg-label-primary' : 'bg-label-secondary') }}">
                                                {{ $module['granted'] }} / {{ $module['total'] }}
                                            </span>
                                        </div>
                                        <div class="progress mb-3" style="height: 6px;">
                                            <div class="progress-bar {{ $module['granted'] === $module['total'] ? 'bg-success' : '' }}" role="progressbar"
                                                style="width: {{ $module['percent'] }}%" aria-valuenow="{{ $module['percent'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($module['permissions'] as $permission)
                                                @if ($permission['granted'])
                                                    <span class="badge bg-label-success role-show-permission">
                                                        <i class="ti ti-check ti-xs me-1"></i>{{ $permission['label'] }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-label-secondary role-show-permission role-show-missing">
                                                        <i class="ti ti-x ti-xs me-1"></i>{{ $permission['label'] }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            {{-- Sign-in platforms --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Users with this role can sign in on') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @foreach ([
                            'can_login_web' => [__('Web dashboard'), 'device-desktop'],
                            'can_login_mobile' => [__('Mobile app'), 'device-mobile'],
                        ] as $platformField => [$platformLabel, $platformIcon])
                            <li class="d-flex align-items-center justify-content-between {{ $loop->last ? '' : 'mb-3' }}">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="avatar avatar-sm">
                                        <span class="avatar-initial rounded bg-label-{{ $role->{$platformField} ? 'success' : 'secondary' }}">
                                            <i class="ti ti-{{ $platformIcon }}"></i>
                                        </span>
                                    </span>
                                    <span class="fw-medium">{{ $platformLabel }}</span>
                                </div>
                                @if ($role->{$platformField})
                                    <span class="badge bg-label-success">{{ __('Allowed') }}</span>
                                @else
                                    <span class="badge bg-label-secondary">{{ __('Not allowed') }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Members --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Members') }}</h5>
                    <span class="badge rounded-pill bg-label-info">{{ $role->users_count }}</span>
                </div>
                <div class="card-body">
                    @forelse ($members as $member)
                        <div class="d-flex align-items-center gap-3 {{ $loop->last ? '' : 'mb-3' }}">
                            <span class="avatar avatar-sm flex-shrink-0">
                                <span class="avatar-initial rounded-circle bg-label-primary">{{ mb_strtoupper(mb_substr($member->full_name, 0, 1)) }}</span>
                            </span>
                            <div class="flex-grow-1 overflow-hidden">
                                @can('users.show')
                                    <a href="{{ route('admin.users.show', $member->id) }}" class="fw-medium text-heading d-block text-truncate">{{ $member->full_name }}</a>
                                @else
                                    <span class="fw-medium text-heading d-block text-truncate">{{ $member->full_name }}</span>
                                @endcan
                                <small class="text-muted d-block text-truncate">{{ $member->email }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">
                            <i class="ti ti-user-off ti-lg d-block mb-2"></i>
                            {{ __('No users hold this role yet') }}
                        </div>
                    @endforelse
                    @if ($role->users_count > $members->count())
                        <small class="text-muted d-block mt-3">{{ __('And :count more', ['count' => $role->users_count - $members->count()]) }}</small>
                    @endif
                </div>
            </div>

            <small class="text-muted d-block mt-3">
                {{ __('Created At') }}: {{ $role->created_at?->format('Y-m-d H:i') }}
                · {{ __('Updated At') }}: {{ $role->updated_at?->format('Y-m-d H:i') }}
            </small>
        </div>
    </div>
@stop
@section('after_foot')
    <style>
        .role-show-hero { background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), .08), transparent 60%); }
        .role-show-missing { opacity: .55; }
        .role-show-permission { font-weight: 500; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('role-show-granted-only')?.addEventListener('change', function (event) {
                const grantedOnly = event.target.checked;
                document.querySelectorAll('.role-show-missing').forEach((badge) => badge.classList.toggle('d-none', grantedOnly));
                document.querySelectorAll('.role-show-module').forEach((module) => {
                    module.classList.toggle('d-none', grantedOnly && module.dataset.granted === '0');
                });
            });
        });
    </script>
@stop
