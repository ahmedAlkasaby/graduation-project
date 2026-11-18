{{--
    The admin menu.

    Layout: `menu-header` section labels group collapsible dropdowns
    (Sneat `menu-toggle` + `menu-sub`). The dropdown whose active child matches
    $class opens on load. Standalone destinations (Home, Cities) stay flat.
    Each entry is still guarded by the permission that covers its route, so a
    user only sees what they can open. A new module adds one <li> in the right
    dropdown and its permission to AdminHelperTrait::$admin_models.
--}}
@php
    // The $class values that live under each collapsible dropdown, so the
    // dropdown containing the current page renders already open.
    $menuGroups = [
        'administration' => ['user', 'role'],
        'organization' => ['department'],
        'employees' => ['employee', 'department_employee', 'employee_history'],
        'devices' => ['device'],
        'attendance' => ['daily_attendance', 'attendance_log', 'attendance_correction'],
        'requests' => ['holiday_request', 'permission_request', 'overtime_request', 'remote_request', 'increase_request', 'approval_step'],
        'payroll' => ['payroll', 'payroll_item', 'payroll_adjustment'],
        'projects_tasks' => ['project', 'task'],
        'hr_setup' => ['job_title', 'work_schedule', 'work_schedule_day', 'employee_work_schedule', 'holiday', 'reminder'],
        'reasons' => ['reason', 'reject_reason'],
        'developer' => ['setting', 'activity_log', 'log'],
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
                    @endcan
                </ul>
            </li>
        @endif

        {{-- Organization: departments --}}
        @can('departments.index')
            <li class="menu-item @if ($openGroup('organization')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-building"></i>
                    <div>{{ __('Organization') }}</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item @if ($activeClass == 'department') active @endif">
                        <a href="{{ route('admin.departments.index') }}" class="menu-link">
                            <div>{{ __('Departments') }}</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        {{-- ===================== HR ===================== --}}
        @if (auth()->user()->can('employees.index') ||
                auth()->user()->can('attendance_logs.index') ||
                auth()->user()->can('daily_attendances.index') ||
                auth()->user()->can('attendance_corrections.index') ||
                auth()->user()->can('permission_requests.index') ||
                auth()->user()->can('overtime_requests.index') ||
                auth()->user()->can('remote_requests.index') ||
                auth()->user()->can('holiday_requests.index') ||
                auth()->user()->can('increase_requests.index') ||
                auth()->user()->can('approval_steps.index') ||
                auth()->user()->can('setting_approvable_request_steps.index') ||
                auth()->user()->can('payrolls.index') ||
                auth()->user()->can('payroll_items.index') ||
                auth()->user()->can('payroll_adjustments.index') ||
                auth()->user()->can('job_titles.index') ||
                auth()->user()->can('reasons.index') ||
                auth()->user()->can('reject_reasons.index') ||
                auth()->user()->can('work_schedules.index') ||
                auth()->user()->can('holidays.index') ||
                auth()->user()->can('reminders.index'))
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">{{ __('HR') }}</span>
            </li>
        @endif

        {{-- Employees --}}
        @if (auth()->user()->can('employees.index') ||
                auth()->user()->can('user_invitations.index'))
            <li class="menu-item @if ($openGroup('employees')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-id-badge-2"></i>
                    <div>{{ __('Employees') }}</div>
                </a>
                <ul class="menu-sub">
                    @can('employees.index')
                        <li class="menu-item @if ($activeClass == 'employee') active @endif">
                            <a href="{{ route('admin.employees.index') }}" class="menu-link">
                                <div>{{ __('Employees') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can('user_invitations.index')
                    <li class="menu-item @if (isset($class) && $class == 'user_invitation') active @endif">
                        <a href="{{ route('admin.user_invitations.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-mail-forward"></i>
                            <div>{{ __('Employee Invitations') }}</div>
                        </a>
                    </li>
                   @endcan
                </ul>
            </li>
        @endif

        {{-- Devices --}}
        @if (auth()->user()->can('devices.index'))
            <li class="menu-item @if ($openGroup('devices')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-devices"></i>
                    <div>{{ __('Devices') }}</div>
                </a>
                <ul class="menu-sub">
                    @can('devices.index')
                        <li class="menu-item @if ($activeClass == 'device' && !request('state')) active @endif">
                            <a href="{{ route('admin.devices.index') }}" class="menu-link">
                                <div>{{ __('All Devices') }}</div>
                            </a>
                        </li>
                        <li class="menu-item @if ($activeClass == 'device' && request('state') == \App\Enums\DeviceStateEnum::Pending->value) active @endif">
                            <a href="{{ route('admin.devices.index', ['state' => \App\Enums\DeviceStateEnum::Pending->value]) }}" class="menu-link">
                                <div>{{ __('Pending Devices') }}</div>
                            </a>
                        </li>
                        <li class="menu-item @if ($activeClass == 'device' && request('state') == \App\Enums\DeviceStateEnum::Verified->value) active @endif">
                            <a href="{{ route('admin.devices.index', ['state' => \App\Enums\DeviceStateEnum::Verified->value]) }}" class="menu-link">
                                <div>{{ __('Verified Devices') }}</div>
                            </a>
                        </li>
                        <li class="menu-item @if ($activeClass == 'device' && request('state') == \App\Enums\DeviceStateEnum::Suspended->value) active @endif">
                            <a href="{{ route('admin.devices.index', ['state' => \App\Enums\DeviceStateEnum::Suspended->value]) }}" class="menu-link">
                                <div>{{ __('Suspended Devices') }}</div>
                            </a>
                        </li>
                        <li class="menu-item @if ($activeClass == 'device' && request('state') == \App\Enums\DeviceStateEnum::Revoked->value) active @endif">
                            <a href="{{ route('admin.devices.index', ['state' => \App\Enums\DeviceStateEnum::Revoked->value]) }}" class="menu-link">
                                <div>{{ __('Revoked Devices') }}</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        {{-- Attendance --}}
        @if (auth()->user()->can('attendance_logs.index') ||
                auth()->user()->can('daily_attendances.index') ||
                auth()->user()->can('attendance_corrections.index'))
            <li class="menu-item @if ($openGroup('attendance')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-clock"></i>
                    <div>{{ __('Attendance') }}</div>
                </a>
                <ul class="menu-sub">
                    @can('daily_attendances.index')
                        <li class="menu-item @if ($activeClass == 'daily_attendance') active @endif">
                            <a href="{{ route('admin.daily_attendances.index') }}" class="menu-link">
                                <div>{{ __('Daily Attendance') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can('attendance_logs.index')
                        <li class="menu-item @if ($activeClass == 'attendance_log') active @endif">
                            <a href="{{ route('admin.attendance_logs.index') }}" class="menu-link">
                                <div>{{ __('Attendance Logs') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can('attendance_corrections.index')
                        <li class="menu-item @if ($activeClass == 'attendance_correction') active @endif">
                            <a href="{{ route('admin.attendance_corrections.index') }}" class="menu-link">
                                <div>{{ __('Attendance Corrections') }}</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        {{-- Requests --}}
        @if (auth()->user()->can('permission_requests.index') ||
                auth()->user()->can('overtime_requests.index') ||
                auth()->user()->can('remote_requests.index') ||
                auth()->user()->can('holiday_requests.index') ||
                auth()->user()->can('increase_requests.index') ||
                auth()->user()->can('approval_steps.index') ||
                auth()->user()->can('setting_approvable_request_steps.index'))
            <li class="menu-item @if ($openGroup('requests')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-file-check"></i>
                    <div>{{ __('Requests') }}</div>
                </a>
                <ul class="menu-sub">
                    @can('holiday_requests.index')
                        <li class="menu-item @if ($activeClass == 'holiday_request') active @endif">
                            <a href="{{ route('admin.holiday_requests.index') }}" class="menu-link">
                                <div>{{ __('Holiday Requests') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can('permission_requests.index')
                        <li class="menu-item @if ($activeClass == 'permission_request') active @endif">
                            <a href="{{ route('admin.permission_requests.index') }}" class="menu-link">
                                <div>{{ __('Permission Requests') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can('overtime_requests.index')
                        <li class="menu-item @if ($activeClass == 'overtime_request') active @endif">
                            <a href="{{ route('admin.overtime_requests.index') }}" class="menu-link">
                                <div>{{ __('Overtime Requests') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can('remote_requests.index')
                        <li class="menu-item @if ($activeClass == 'remote_request') active @endif">
                            <a href="{{ route('admin.remote_requests.index') }}" class="menu-link">
                                <div>{{ __('Remote Requests') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can('increase_requests.index')
                        <li class="menu-item @if ($activeClass == 'increase_request') active @endif">
                            <a href="{{ route('admin.increase_requests.index') }}" class="menu-link">
                                <div>{{ __('Increase Requests') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can('approval_steps.index')
                        <li class="menu-item @if ($activeClass == 'approval_step') active @endif">
                            <a href="{{ route('admin.approval_steps.index') }}" class="menu-link">
                                <div>{{ __('Approval Steps') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can('setting_approvable_request_steps.index')
                        <li class="menu-item @if ($activeClass == 'setting_approvable_request_step') active @endif">
                            <a href="{{ route('admin.setting_approvable_request_steps.index') }}" class="menu-link">
                                <div>{{ __('Approval Flows') }}</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        {{-- Payroll --}}
        @if (auth()->user()->can('payrolls.index') ||
                auth()->user()->can('payroll_items.index') ||
                auth()->user()->can('payroll_adjustments.index'))
            <li class="menu-item @if ($openGroup('payroll')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-cash"></i>
                    <div>{{ __('Payroll') }}</div>
                </a>
                <ul class="menu-sub">
                    @can('payrolls.index')
                        <li class="menu-item @if ($activeClass == 'payroll') active @endif">
                            <a href="{{ route('admin.payrolls.index') }}" class="menu-link">
                                <div>{{ __('Payrolls') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can('payroll_items.index')
                        <li class="menu-item @if ($activeClass == 'payroll_item') active @endif">
                            <a href="{{ route('admin.payroll_items.index') }}" class="menu-link">
                                <div>{{ __('Payroll Items') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can('payroll_adjustments.index')
                        <li class="menu-item @if ($activeClass == 'payroll_adjustment') active @endif">
                            <a href="{{ route('admin.payroll_adjustments.index') }}" class="menu-link">
                                <div>{{ __('Payroll Adjustments') }}</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        {{-- HR Setup --}}
        @if (auth()->user()->can('job_titles.index') ||
                auth()->user()->can('reasons.index') ||
                auth()->user()->can('reject_reasons.index') ||
                auth()->user()->can('work_schedules.index') ||
                auth()->user()->can('holidays.index') ||
                auth()->user()->can('reminders.index'))
            <li class="menu-item @if ($openGroup('hr_setup')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-tool"></i>
                    <div>{{ __('HR Setup') }}</div>
                </a>
                <ul class="menu-sub">
                    @can('job_titles.index')
                        <li class="menu-item @if ($activeClass == 'job_title') active @endif">
                            <a href="{{ route('admin.job_titles.index') }}" class="menu-link">
                                <div>{{ __('Job Titles') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can('work_schedules.index')
                        <li class="menu-item @if ($activeClass == 'work_schedule') active @endif">
                            <a href="{{ route('admin.work_schedules.index') }}" class="menu-link">
                                <div>{{ __('Work Schedules') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can('holidays.index')
                        <li class="menu-item @if ($activeClass == 'holiday') active @endif">
                            <a href="{{ route('admin.holidays.index') }}" class="menu-link">
                                <div>{{ __('Holidays') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can('reminders.index')
                        <li class="menu-item @if ($activeClass == 'reminder') active @endif">
                            <a href="{{ route('admin.reminders.index') }}" class="menu-link">
                                <div>{{ __('Reminders') }}</div>
                            </a>
                        </li>
                        <li class="menu-item @if ($activeClass == 'my_reminder') active @endif">
                            <a href="{{ route('admin.my_reminders.index') }}" class="menu-link">
                                <div>{{ __('My Reminders') }}</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        {{-- Reasons --}}
        @if (auth()->user()->can('reasons.index') || auth()->user()->can('reject_reasons.index'))
            <li class="menu-item @if ($openGroup('reasons')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-list-check"></i>
                    <div>{{ __('Reasons') }}</div>
                </a>
                <ul class="menu-sub">
                    @can('reasons.index')
                        <li class="menu-item @if ($activeClass == 'reason') active @endif">
                            <a href="{{ route('admin.reasons.index') }}" class="menu-link">
                                <div>{{ __('Reasons') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can('reject_reasons.index')
                        <li class="menu-item @if ($activeClass == 'reject_reason') active @endif">
                            <a href="{{ route('admin.reject_reasons.index') }}" class="menu-link">
                                <div>{{ __('Reject Reasons') }}</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        {{-- ===================== Work ===================== --}}
        @if (auth()->user()->can('projects.index') ||
                auth()->user()->can('tasks.index') ||
                auth()->user()->can('cities.index'))
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">{{ __('Work') }}</span>
            </li>
        @endif

        {{-- Projects & Tasks --}}
        @if (auth()->user()->can('projects.index') || auth()->user()->can('tasks.index'))
            <li class="menu-item @if ($openGroup('projects_tasks')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-checklist"></i>
                    <div>{{ __('Projects & Tasks') }}</div>
                </a>
                <ul class="menu-sub">
                    @can('projects.index')
                        <li class="menu-item @if ($activeClass == 'project') active @endif">
                            <a href="{{ route('admin.projects.index') }}" class="menu-link">
                                <div>{{ __('Projects') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can('tasks.index')
                        <li class="menu-item @if ($activeClass == 'task') active @endif">
                            <a href="{{ route('admin.tasks.board') }}" class="menu-link">
                                <div>{{ __('Tasks') }}</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        {{-- Cities (standalone) --}}
        @can('cities.index')
            <li class="menu-item @if ($activeClass == 'city') active @endif">
                <a href="{{ route('admin.cities.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-map-pin"></i>
                    <div>{{ __('Cities') }}</div>
                </a>
            </li>
        @endcan

        {{-- ===================== Developer — owners only ===================== --}}
        @if (in_array(auth()->user()->type, $access_all_type ?? []))
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">{{ __('Developer') }}</span>
            </li>

            <li class="menu-item @if ($openGroup('developer')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons ti ti-code"></i>
                    <div>{{ __('Developer') }}</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item @if ($activeClass == 'setting') active @endif">
                        <a href="{{ route('admin.settings.developer.setting') }}" class="menu-link">
                            <div>{{ __('Setting') }}</div>
                        </a>
                    </li>

                    {{-- Laravel Pulse. Guarded by Route::has so the link disappears
                         cleanly when PULSE_ENABLED=false rather than throwing. --}}
                    @if (Route::has('pulse'))
                        <li class="menu-item">
                            <a href="{{ route('pulse') }}" class="menu-link">
                                <div>{{ __('Pulse') }}</div>
                            </a>
                        </li>
                    @endif

                    {{-- Translations --}}
                    @can('translations.index')
                        <li class="menu-item @if (request()->is(config('translation-manager.route.prefix') . '*')) active @endif">
                            <a href="{{ url(config('translation-manager.route.prefix')) }}" class="menu-link">
                                <div>{{ __('Translations') }}</div>
                            </a>
                        </li>
                    @endcan

                    {{-- Logs. Two tables, two questions: what a user did, and what changed. --}}
                    @can('activity_logs.index')
                        <li class="menu-item @if ($activeClass == 'activity_log') active @endif">
                            <a href="{{ route('admin.activity_logs.index') }}" class="menu-link">
                                <div>{{ __('Activity Logs') }}</div>
                            </a>
                        </li>
                    @endcan
                    @can('logs.index')
                        <li class="menu-item @if ($activeClass == 'log') active @endif">
                            <a href="{{ route('admin.logs.index') }}" class="menu-link">
                                <div>{{ __('Data Change Logs') }}</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif
    </ul>
</aside>
