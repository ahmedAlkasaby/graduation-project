{{--
    The admin menu.

    Layout: `menu-header` section labels group collapsible dropdowns
    (Sneat `menu-toggle` + `menu-sub`). The dropdown whose active child matches
    $class opens on load. The standalone Home entry stays flat.
    Each entry is still guarded by the permission that covers its route, so a
    user only sees what they can open. A new module adds one <li> in the right
    dropdown and its permission to AdminHelperTrait::$admin_models.
--}}
@php
    // The $class values that live under each collapsible dropdown, so the
    // dropdown containing the current page renders already open.
    $menuGroups = [
        'administration' => ['user', 'role', 'permission'],
        'locations' => ['city', 'region'],
        'case_management' => ['charity_case', 'case_assistance'],
    ];
    $activeClass = $class ?? null;
    $openGroup = fn (string $group) => in_array($activeClass, $menuGroups[$group] ?? [], true);
@endphp
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

        {{-- ===================== General ===================== --}}
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">{{ __('General') }}</span>
        </li>

        {{-- Dashboard --}}
        <li class="menu-item @if ($activeClass == 'home') active @endif">
            <a href="{{ route('admin.index') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div>{{ __('Home') }}</div>
            </a>
        </li>

        {{-- Administration: users & roles --}}
        @if (auth()->user()->can('users.index') || auth()->user()->can('roles.index'))
            <li class="menu-item @if ($openGroup('administration')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-settings-cog"></i>
                    <div>{{ __('Administration') }}</div>
                </a>
                <ul class="menu-sub">
                    @can('users.index')
                        <li class="menu-item @if ($activeClass == 'user') active @endif">
                            <a href="{{ route('admin.users.index') }}" class="menu-link">
                                <div>{{ __('Users') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can('roles.index')
                        <li class="menu-item @if ($activeClass == 'role') active @endif">
                            <a href="{{ route('admin.roles.index') }}" class="menu-link">
                                <div>{{ __('Roles') }}</div>
                            </a>
                        </li>
                        <li class="menu-item @if ($activeClass == 'permission') active @endif">
                            <a href="{{ route('admin.permissions.index') }}" class="menu-link">
                                <div>{{ __('Permissions') }}</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        {{-- Locations: cities (governorates) & their regions --}}
        @if (auth()->user()->can('cities.index') || auth()->user()->can('regions.index'))
            <li class="menu-item @if ($openGroup('locations')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-map-pin"></i>
                    <div>{{ __('Locations') }}</div>
                </a>
                <ul class="menu-sub">
                    @can('cities.index')
                        <li class="menu-item @if ($activeClass == 'city') active @endif">
                            <a href="{{ route('admin.cities.index') }}" class="menu-link">
                                <div>{{ __('Cities') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can('regions.index')
                        <li class="menu-item @if ($activeClass == 'region') active @endif">
                            <a href="{{ route('admin.regions.index') }}" class="menu-link">
                                <div>{{ __('Regions') }}</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        {{-- Case management: beneficiary profiles and their assistances --}}
        @if (auth()->user()->can('charity_cases.index') || auth()->user()->can('case_assistances.index'))
            <li class="menu-item @if ($openGroup('case_management')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-heart-handshake"></i>
                    <div>{{ __('Case Management') }}</div>
                </a>
                <ul class="menu-sub">
                    @can('case_assistances.index')
                        <li class="menu-item @if ($activeClass == 'case_assistance') active @endif">
                            <a href="{{ route('admin.case_assistances.index') }}" class="menu-link">
                                <div>{{ __('Assistances') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can('charity_cases.index')
                        <li class="menu-item @if ($activeClass == 'charity_case') active @endif">
                            <a href="{{ route('admin.charity_cases.index') }}" class="menu-link">
                                <div>{{ __('Shared Cases') }}</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif
    </ul>
</aside>
