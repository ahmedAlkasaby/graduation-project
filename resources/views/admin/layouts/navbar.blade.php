{{-- Top bar: language, theme, shortcuts, and the signed-in user's menu. --}}
<nav class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="ti ti-menu-2 ti-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

        <ul class="navbar-nav flex-row align-items-center ms-auto">

            {{-- Language --}}
            <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class="ti ti-language rounded-circle ti-md"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item @if (app()->getLocale() == 'en') active @endif"
                            href="javascript:void(0);" data-language="en" data-text-direction="ltr"
                            onclick="changeLang('en')">
                            <span class="align-middle">{{ __('English') }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item @if (app()->getLocale() == 'ar') active @endif"
                            href="javascript:void(0);" data-language="ar" data-text-direction="rtl"
                            onclick="changeLang('ar')">
                            <span class="align-middle">{{ __('Arabic') }}</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Theme --}}
            <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class="ti ti-md ti-moon"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="light"
                            onclick="changeTheme('light')">
                            <span class="align-middle"><i class="ti ti-sun me-2"></i>{{ __('Light') }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="dark"
                            onclick="changeTheme('dark')">
                            <span class="align-middle"><i class="ti ti-moon me-2"></i>{{ __('Dark') }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="semi-dark"
                            onclick="changeTheme('semi-dark')">
                            <span class="align-middle"><i class="ti ti-moon me-2"></i>{{ __('Semi Dark') }}</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Shortcuts --}}
            <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-2 me-xl-0">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
                    data-bs-auto-close="outside" aria-expanded="false">
                    <i class="ti ti-layout-grid-add ti-md"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end py-0">
                    <div class="dropdown-menu-header border-bottom">
                        <div class="dropdown-header d-flex align-items-center py-3">
                            <h5 class="text-body mb-0 me-auto">{{ __('Shortcuts') }}</h5>
                        </div>
                    </div>
                    <div class="dropdown-shortcuts-list scrollable-container">
                        <div class="row row-bordered overflow-visible g-0">
                            <div class="dropdown-shortcuts-item col">
                                <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                    <i class="ti ti-chart-bar fs-4"></i>
                                </span>
                                <a href="{{ route('admin.index') }}" class="stretched-link">{{ __('Dashboard') }}</a>
                                <small class="text-muted mb-0">{{ __('Statistics') }}</small>
                            </div>

                            @can('users.index')
                                <div class="dropdown-shortcuts-item col">
                                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                        <i class="ti ti-users fs-4"></i>
                                    </span>
                                    <a href="{{ route('admin.users.index') }}"
                                        class="stretched-link">{{ __('Users') }}</a>
                                    <small class="text-muted mb-0">{{ __('Users Management') }}</small>
                                </div>
                            @endcan
                        </div>

                        <div class="row row-bordered overflow-visible g-0">
                            @can('roles.index')
                                <div class="dropdown-shortcuts-item col">
                                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                        <i class="ti ti-lock fs-4"></i>
                                    </span>
                                    <a href="{{ route('admin.roles.index') }}"
                                        class="stretched-link">{{ __('Roles') }}</a>
                                    <small class="text-muted mb-0">{{ __('Roles Management') }}</small>
                                </div>
                            @endcan

                            @if (in_array(auth()->user()->type, $access_all_type ?? []))
                                <div class="dropdown-shortcuts-item col">
                                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                        <i class="ti ti-settings fs-4"></i>
                                    </span>
                                    <a href="{{ route('admin.settings.index') }}"
                                        class="stretched-link">{{ __('Setting') }}</a>
                                    <small class="text-muted mb-0">{{ __('App Settings') }}</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </li>

            {{-- Signed-in user --}}
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="{{ $user_account?->image ?: asset('assets/img/avatars/1.png') }}"
                            class="h-auto rounded-circle" alt="{{ __('User Image') }}">
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.users.edit', $user_account->id) }}">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="{{ $user_account?->image ?: asset('assets/img/avatars/1.png') }}"
                                            class="img-circle" alt="{{ __('User Image') }}">
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-medium d-block">{{ $user_account?->full_name }}</span>
                                    <small class="text-muted">{{ labelForValue($user_account?->type) }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.users.edit', $user_account->id) }}">
                            <i class="ti ti-user-check me-2 ti-sm"></i>
                            <span class="align-middle">{{ __('Profile') }}</span>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            style="cursor: pointer;">
                            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST"
                                style="display: none;">
                                {{ csrf_field() }}
                            </form>
                            <i class="ti ti-logout me-2 ti-sm"></i>
                            <span class="align-middle">{{ __('Log Out') }}</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
