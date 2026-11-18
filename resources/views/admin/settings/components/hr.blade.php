<div id="hr-settings" class="content">
    <div class="row g-3">

        {{-- ── Work Schedule ───────────────────────────────────────────── --}}
        <div class="col-12">
            <h6 class="text-muted fw-semibold mb-0">{{ __('Work Schedule') }}</h6>
            <hr class="mt-1">
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.text', [
                'text_name'  => 'start_time',
                'text_value' => $setting['start_time'] ?? '09:00',
                'label_name' => __('Start Time'),
                'not_req'    => true,
                'placeholder' => '09:00',
            ])
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.text', [
                'text_name'  => 'latest_start_time',
                'text_value' => $setting['latest_start_time'] ?? '09:30',
                'label_name' => __('Latest Start Time'),
                'not_req'    => true,
                'placeholder' => '09:30',
            ])
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'required_work_hours',
                'number_value' => $setting['required_work_hours'] ?? 8,
                'label'        => __('Required Work Hours'),
                'not_req'      => true,
                'min'          => 0,
                'max'          => 24,
            ])
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'break_minutes',
                'number_value' => $setting['break_minutes'] ?? 60,
                'label'        => __('Break Minutes'),
                'not_req'      => true,
                'min'          => 0,
                'max'          => 480,
            ])
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'attendance_grace_minutes',
                'number_value' => $setting['attendance_grace_minutes'] ?? 15,
                'label'        => __('Attendance Grace Minutes'),
                'not_req'      => true,
                'min'          => 0,
                'max'          => 240,
            ])
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.select', [
                'label_name'      => __('Default Work Location'),
                'select_name'     => 'default_work_location',
                'select_value'    => $setting['default_work_location'] ?? \App\Enums\WorkLocationEnum::OnSite->value,
                'select_function' => \App\Enums\WorkLocationEnum::options(),
                'not_req'         => true,
            ])
            <small class="text-muted d-block mt-1">{{ __('Used for employees and schedule days that do not set their own.') }}</small>
        </div>

        {{-- Weekend Days (checkboxes) --}}
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">{{ __('Weekend Days') }}</label>
                @php
                    $weekendDays = array_map('intval', explode(',', $setting['weekend_days'] ?? '5,6'));
                    $dayLabels   = \App\Enums\WeekDayEnum::options();
                @endphp
                <div class="d-flex flex-wrap gap-3">
                    @foreach($dayLabels as $value => $label)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                name="weekend_days[]"
                                value="{{ $value }}"
                                id="weekend_day_{{ $value }}"
                                {{ in_array($value, $weekendDays) ? 'checked' : '' }}>
                            <label class="form-check-label" for="weekend_day_{{ $value }}">
                                {{ $label }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Company Location (Geofence) ─────────────────────────────── --}}
        <div class="col-12 mt-2">
            <h6 class="text-muted fw-semibold mb-0">{{ __('Company Location') }}</h6>
            <hr class="mt-1">
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'company_latitude',
                'number_value' => $setting['company_latitude'] ?? 0,
                'label'        => __('Latitude'),
                'not_req'      => true,
                'step'         => 'any',
                'min'          => -90,
                'max'          => 90,
            ])
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'company_longitude',
                'number_value' => $setting['company_longitude'] ?? 0,
                'label'        => __('Longitude'),
                'not_req'      => true,
                'step'         => 'any',
                'min'          => -180,
                'max'          => 180,
            ])
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'company_radius',
                'number_value' => $setting['company_radius'] ?? 100,
                'label'        => __('Radius (meters)'),
                'not_req'      => true,
                'step'         => 'any',
                'min'          => 1,
            ])
        </div>

        {{-- ── QR Attendance ────────────────────────────────────────────── --}}
        <div class="col-12 mt-2">
            <h6 class="text-muted fw-semibold mb-0">{{ __('QR Attendance') }}</h6>
            <hr class="mt-1">
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'attendance_qr_ttl_seconds',
                'number_value' => $setting['attendance_qr_ttl_seconds'] ?? 45,
                'label'        => __('QR Code Lifetime (seconds)'),
                'not_req'      => true,
                'min'          => 10,
                'max'          => 3600,
            ])
        </div>

        {{-- ── Monthly Limits ───────────────────────────────────────────── --}}
        <div class="col-12 mt-2">
            <h6 class="text-muted fw-semibold mb-0">{{ __('Monthly Limits') }}</h6>
            <hr class="mt-1">
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'monthly_permission_morning_hours',
                'number_value' => $setting['monthly_permission_morning_hours'] ?? 2,
                'label'        => __('Morning Permission Hours / Month'),
                'not_req'      => true,
                'min'          => 0,
            ])
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'monthly_permission_evening_hours',
                'number_value' => $setting['monthly_permission_evening_hours'] ?? 2,
                'label'        => __('Evening Permission Hours / Month'),
                'not_req'      => true,
                'min'          => 0,
            ])
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'permission_max_minutes_per_request',
                'number_value' => $setting['permission_max_minutes_per_request'] ?? 0,
                'label'        => __('Max Permission Minutes at Once (0 = no limit)'),
                'not_req'      => true,
                'min'          => 0,
            ])
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'overtime_max_minutes_per_request',
                'number_value' => $setting['overtime_max_minutes_per_request'] ?? 0,
                'label'        => __('Max Overtime Minutes at Once (0 = no limit)'),
                'not_req'      => true,
                'min'          => 0,
            ])
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'monthly_remote_days_limit',
                'number_value' => $setting['monthly_remote_days_limit'] ?? 4,
                'label'        => __('Remote Days / Month'),
                'not_req'      => true,
                'min'          => 0,
            ])
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'holiday_min_service_months',
                'number_value' => $setting['holiday_min_service_months'] ?? 6,
                'label'        => __('Months of Service Before Holidays'),
                'not_req'      => true,
                'min'          => 0,
            ])
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'holiday_regular_limit_per_year',
                'number_value' => $setting['holiday_regular_limit_per_year'] ?? 21,
                'label'        => __('Regular Holiday Days / Year'),
                'not_req'      => true,
                'min'          => 0,
            ])
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'holiday_regular_max_per_month',
                'number_value' => $setting['holiday_regular_max_per_month'] ?? 0,
                'label'        => __('Max Regular Holiday Days / Month (0 = no limit)'),
                'not_req'      => true,
                'min'          => 0,
            ])
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'holiday_casual_limit_per_year',
                'number_value' => $setting['holiday_casual_limit_per_year'] ?? 6,
                'label'        => __('Casual Holiday Days / Year'),
                'not_req'      => true,
                'min'          => 0,
            ])
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'holiday_casual_max_per_request',
                'number_value' => $setting['holiday_casual_max_per_request'] ?? 2,
                'label'        => __('Max Casual Holiday Days at Once (0 = no limit)'),
                'not_req'      => true,
                'min'          => 0,
            ])
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.select', [
                'label_name'      => __('Day Swap Period'),
                'select_name'     => 'day_swap_period',
                'select_value'    => $setting['day_swap_period'] ?? \App\Enums\DaySwapPeriodEnum::Month->value,
                'select_function' => \App\Enums\DaySwapPeriodEnum::options(),
                'not_req'         => true,
            ])
            <small class="text-muted d-block mt-1">{{ __('Both dates of a day swap must fall in this one week or month.') }}</small>
        </div>

        {{-- ── Payroll ──────────────────────────────────────────────────── --}}
        <div class="col-12 mt-2">
            <h6 class="text-muted fw-semibold mb-0">{{ __('Payroll') }}</h6>
            <hr class="mt-1">
        </div>

        {{-- Payroll Working Days (checkboxes) --}}
        <div class="col-md-12">
            <div class="mb-3">
                <label class="form-label">{{ __('Payroll Working Days') }}</label>
                @php
                    $payrollWorkingDays = array_map('intval', explode(',', $setting['payroll_working_days'] ?? '0,1,2,3,4'));
                    $dayLabels = \App\Enums\WeekDayEnum::options();
                @endphp
                <div class="d-flex flex-wrap gap-3">
                    @foreach ($dayLabels as $value => $label)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="payroll_working_days[]"
                                value="{{ $value }}" id="payroll_working_day_{{ $value }}"
                                {{ in_array($value, $payrollWorkingDays) ? 'checked' : '' }}>
                            <label class="form-check-label" for="payroll_working_day_{{ $value }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
                <small class="text-muted d-block mt-1">{{ __('Used to derive the daily rate (basic salary ÷ working days in the month).') }}</small>
            </div>
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'payroll_hours_per_day',
                'number_value' => $setting['payroll_hours_per_day'] ?? 8,
                'label'        => __('Hours per Day'),
                'not_req'      => true,
                'step'         => 'any',
                'min'          => 1,
                'max'          => 24,
            ])
            <small class="text-muted d-block mt-1">{{ __('Divides the daily rate into an hourly rate for overtime.') }}</small>
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.select', [
                'label_name'      => __('Overtime Enabled'),
                'select_name'     => 'overtime_enabled',
                'select_value'    => $setting['overtime_enabled'] ?? '1',
                'select_function' => ['1' => __('Yes'), '0' => __('No')],
                'not_req'         => true,
            ])
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'overtime_rate',
                'number_value' => $setting['overtime_rate'] ?? 1.5,
                'label'        => __('Overtime Rate (× hourly)'),
                'not_req'      => true,
                'step'         => 'any',
                'min'          => 1,
                'max'          => 5,
            ])
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.select', [
                'label_name'      => __('Late Deduction Enabled'),
                'select_name'     => 'late_deduction_enabled',
                'select_value'    => $setting['late_deduction_enabled'] ?? '1',
                'select_function' => ['1' => __('Yes'), '0' => __('No')],
                'not_req'         => true,
            ])
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.select', [
                'label_name'      => __('Penalty Deduction Enabled'),
                'select_name'     => 'penalty_enabled',
                'select_value'    => $setting['penalty_enabled'] ?? '1',
                'select_function' => ['1' => __('Yes'), '0' => __('No')],
                'not_req'         => true,
            ])
            <small class="text-muted d-block mt-1">{{ __('When off, penalty adjustments are not deducted from payroll.') }}</small>
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'deduction_quarter_day_after_late_minutes',
                'number_value' => $setting['deduction_quarter_day_after_late_minutes'] ?? 30,
                'label'        => __('Quarter-day Deduction After Late Minutes'),
                'not_req'      => true,
                'min'          => 0,
            ])
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'deduction_half_day_after_late_minutes',
                'number_value' => $setting['deduction_half_day_after_late_minutes'] ?? 120,
                'label'        => __('Half-day Deduction After Late Minutes'),
                'not_req'      => true,
                'min'          => 0,
            ])
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'payroll_tax_rate',
                'number_value' => $setting['payroll_tax_rate'] ?? 0,
                'label'        => __('Tax Rate (0 - 1)'),
                'not_req'      => true,
                'step'         => 'any',
                'min'          => 0,
                'max'          => 1,
            ])
            <small class="text-muted d-block mt-1">{{ __('A fraction: 0.10 = 10%. Applied to basic salary + additions.') }}</small>
        </div>

        <div class="col-md-4">
            @include('admin.layouts.forms.fields.number', [
                'number_name'  => 'payroll_insurance_rate',
                'number_value' => $setting['payroll_insurance_rate'] ?? 0,
                'label'        => __('Insurance Rate (0 - 1)'),
                'not_req'      => true,
                'step'         => 'any',
                'min'          => 0,
                'max'          => 1,
            ])
            <small class="text-muted d-block mt-1">{{ __('A fraction: 0.05 = 5%. Applied to the basic salary.') }}</small>
        </div>

    </div>
</div>
