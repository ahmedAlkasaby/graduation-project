<div class="col-12 mb-4">
    <div class="bs-stepper wizard-numbered mt-2">
        <div class="bs-stepper-content">
<div class="row g-3 mt-2 mb-4">
    <div class="col-12">
        <h6 class="fw-semibold mb-0">{{ __('Account') }}</h6>
        <hr class="mt-2 mb-0">
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('User'),
            'label_req' => true,
            'select_name' => 'user_id',
            'select_value' => isset($employee) ? $employee->user_id : null,
            'select_function' => $users ?? [],
        ])
        <small class="text-muted d-block mt-1">
            {{ __('Only active users who are not linked to an employee are listed.') }}
            @if (count($users ?? []) <= 1)
                <a href="{{ route('admin.users.create') }}" target="_blank">{{ __('Create User') }}</a>
            @endif
        </small>
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'code',
            'text_value' => isset($employee) ? $employee->code : null,
            'label_name' => __('Employee Code'),
            'label_req' => true,
            'text_id' => 'employee_code',
        ])
        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="generate-employee-code"
            data-url="{{ route('admin.employees.generate_code') }}">
            <i class="ti ti-refresh"></i> {{ __('Generate') }}
        </button>
    </div>

    <div class="col-12 mt-4">
        <h6 class="fw-semibold mb-0">{{ __('Personal Information') }}</h6>
        <hr class="mt-2 mb-0">
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.text', [
            'text_name' => 'national_id',
            'text_value' => isset($employee) ? $employee->national_id : null,
            'label_name' => __('National Id'),
            'label_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Religion'),
            'select_name' => 'religion',
            'select_value' => isset($employee) ? $employee->religion?->value : null,
            'select_function' => \App\Enums\ReligionEnum::optionsWithPlaceholder(__('Select'), ''),
            'not_req' => true,
        ])
    </div>

    <div class="col-12 mt-4">
        <h6 class="fw-semibold mb-0">{{ __('Employment Information') }}</h6>
        <hr class="mt-2 mb-0">
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Employment Type'),
            'label_req' => true,
            'select_name' => 'type',
            'select_value' => isset($employee) ? $employee->type?->value : null,
            'select_function' => \App\Enums\EmploymentTypeEnum::options(),
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Job Title'),
            'select_name' => 'job_title_id',
            'select_value' => isset($employee) ? $employee->job_title_id : null,
            'select_function' => $jobTitles ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Manager'),
            'select_name' => 'manager_id',
            'select_value' => isset($employee) ? $employee->manager_id : null,
            'select_function' => $managers ?? [],
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Work Location'),
            'not_req' => true,
            'select_name' => 'work_location',
            'select_value' => isset($employee) ? $employee->work_location?->value : null,
            'select_function' => \App\Enums\WorkLocationEnum::optionsWithPlaceholder(__('Company default')),
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'monthly_required_minutes',
            'number_value' => isset($employee) ? $employee->monthly_required_minutes : null,
            'label_name' => __('Monthly Required Minutes'),
            'not_req' => true,
        ])
        <small class="text-muted d-block mt-1">{{ __('Setting this makes the employee work flexible hours.') }}</small>
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date', [
            'date_name' => 'hire_date',
            'date_value' => isset($employee) ? $employee->hire_date?->toDateString() : null,
            'label_name' => __('Hire Date'),
            'label_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.date', [
            'date_name' => 'termination_date',
            'date_value' => isset($employee) ? $employee->termination_date?->toDateString() : null,
            'label_name' => __('Termination Date'),
            'not_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.number', [
            'number_name' => 'basic_salary',
            'number_value' => isset($employee) ? $employee->basic_salary : 0,
            'label_name' => __('Basic Salary'),
            'label_req' => true,
        ])
    </div>
    <div class="col-md-6">
        @include('admin.layouts.forms.fields.select', [
            'label_name' => __('Always Approve Requests'),
            'select_name' => 'always_approve_requests',
            'select_value' => isset($employee) ? (int) $employee->always_approve_requests : 0,
            'select_function' => $yesNoOptions,
            'not_req' => true,
        ])
        <small class="text-muted d-block mt-1">{{ __('Requests of this employee are approved as soon as they are submitted.') }}</small>
    </div>
</div>
@include('admin.layouts.forms.footer')

@push('scripts')
    <script>
        var $employeeCode = $('#employee_code');
        var $generateButton = $('#generate-employee-code');

        function generateEmployeeCode() {
            $generateButton.prop('disabled', true);

            $.getJSON($generateButton.data('url'))
                .done(function(response) {
                    $employeeCode.val(response.code).trigger('input');
                })
                .always(function() {
                    $generateButton.prop('disabled', false);
                });
        }

        $generateButton.on('click', generateEmployeeCode);

        @if (!isset($employee))
            if (!$employeeCode.val()) {
                generateEmployeeCode();
            }
        @endif
    </script>
@endpush
