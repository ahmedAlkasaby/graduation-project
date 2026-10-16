{{--
    The admin menu.

    Each entry is guarded by the permission that covers its route, so a user
    only sees what they can actually open. A new module adds one <li> here and
    its permission to AdminHelperTrait::$admin_models.
--}}
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('admin.index') }}" class="app-brand-link">
            @if ($siteLogo)
                <span class="app-brand-logo demo">
                    @include('admin.layouts.image-head')
                </span>
            @else
                {{-- No logo uploaded: fall back to the site title. --}}
                <span class="app-brand-text demo menu-text fw-bold"
                    style="padding: 10px;">{{ $company_name ?? config('app.name') }}</span>
            @endif
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="align-middle ti menu-toggle-icon d-none d-xl-block ti-sm"></i>
            <i class="align-middle ti ti-x d-block d-xl-none ti-sm"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="py-1 menu-inner">

        {{-- Dashboard --}}
        <li class="menu-item @if (isset($class) && $class == 'home') active @endif">
            <a href="{{ route('admin.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div>{{ __('Home') }}</div>
            </a>
        </li>

        {{-- Users --}}
        @can('users.index')
            <li class="menu-item @if (isset($class) && $class == 'user') active @endif">
                <a href="{{ route('admin.users.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-users"></i>
                    <div>{{ __('Users') }}</div>
                </a>
            </li>
        @endcan

        {{-- Roles --}}
        @can('roles.index')
            <li class="menu-item @if (isset($class) && $class == 'role') active @endif">
                <a href="{{ route('admin.roles.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-components"></i>
                    <div>{{ __('Roles') }}</div>
                </a>
            </li>
        @endcan

        {{-- Cities --}}
        @can('cities.index')
            <li class="menu-item @if (isset($class) && $class == 'city') active @endif">
                <a href="{{ route('admin.cities.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-map-pin"></i>
                    <div>{{ __('Cities') }}</div>
                </a>
            </li>
        @endcan

        {
        @can('translations.index')
            <li class="menu-item @if (request()->is(config('translation-manager.route.prefix') . '*')) active @endif">
                <a href="{{ url(config('translation-manager.route.prefix')) }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-language"></i>
                    <div>{{ __('Translations') }}</div>
                </a>
            </li>
        @endcan

        {{-- Logs. Two tables, two questions: what a user did, and what changed. --}}
        @if (auth()->user()->can('activity_logs.index') || auth()->user()->can('logs.index'))
            <li class="menu-item @if (isset($class) && in_array($class, ['activity_log', 'log'])) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-history"></i>
                    <div>{{ __('Logs') }}</div>
                </a>
                <ul class="menu-sub">
                    @can('activity_logs.index')
                        <li class="menu-item @if (isset($class) && $class == 'activity_log') active @endif">
                            <a href="{{ route('admin.activity_logs.index') }}" class="menu-link">
                                <div>{{ __('Activity Logs') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can('logs.index')
                        <li class="menu-item @if (isset($class) && $class == 'log') active @endif">
                            <a href="{{ route('admin.logs.index') }}" class="menu-link">
                                <div>{{ __('Data Change Logs') }}</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        {{-- Settings — owners only --}}
        @if (in_array(auth()->user()->type, $access_all_type ?? []))
            <li class="menu-item @if (isset($class) && $class == 'setting') active @endif">
                <a href="{{ route('admin.settings.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-settings"></i>
                    <div>{{ __('Setting') }}</div>
                </a>
            </li>

            <li class="menu-item @if (isset($class) && in_array($class, ['developer', 'image-setting'])) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-code"></i>
                    <div>{{ __('Developer') }}</div>
                </a>
                <ul class="menu-sub">
                    {{-- Laravel Pulse. Guarded by Route::has so the link disappears
                         cleanly when PULSE_ENABLED=false rather than throwing. --}}
                    @if (Route::has('pulse'))
                        <li class="menu-item">
                            <a href="{{ route('pulse') }}" class="menu-link">
                                <div>{{ __('Pulse') }}</div>
                            </a>
                        </li>
                    @endif
                    <li class="menu-item">
                        <a href="{{ route('admin.optimize.clear') }}" class="menu-link">
                            <div>{{ __('Optimize') }}</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('admin.optimize') }}" class="menu-link">
                            <div>{{ __('Cache') }}</div>
                        </a>
                    </li>
                    <li class="menu-item @if (isset($class) && $class == 'developer') active @endif">
                        <a href="{{ route('admin.developer.setting') }}" class="menu-link">
                            <div>{{ __('Setting') }}</div>
                        </a>
                    </li>
                    <li class="menu-item @if (isset($class) && $class == 'image-setting') active @endif">
                        <a href="{{ route('admin.images.setting') }}" class="menu-link">
                            <div>{{ __('Image Setting') }}</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endif

    </ul>
</aside>
