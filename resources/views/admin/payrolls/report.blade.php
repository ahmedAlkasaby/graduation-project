@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title')
    {{ __('Payroll Reports') }}
@stop
@section('content')
    @php
        $t = $report['totals'];
        $months = collect(range(1, 12))->mapWithKeys(
            fn ($m) => [$m => \Illuminate\Support\Carbon::create(null, $m, 1)->translatedFormat('F')],
        );
        $years = collect(range(now()->year, now()->year - 5))->mapWithKeys(fn ($y) => [$y => $y]);
    @endphp

    {{-- Filter / scope form --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Payroll Reports') }}</h5>
            <a href="{{ route('admin.payrolls.report_export', request()->query()) }}" class="btn btn-outline-success">
                <i class="ti ti-download me-1"></i>{{ __('Export CSV') }}
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.payrolls.report') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">{{ __('Report') }}</label>
                    <select name="scope" class="form-select">
                        @foreach ([
            'current_month' => __('Current month'),
            'monthly' => __('A month'),
            'yearly' => __('A year'),
            'employee' => __('One employee'),
        ] as $value => $label)
                            <option value="{{ $value }}" @selected($scope === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Year') }}</label>
                    <select name="year" class="form-select">
                        @foreach ($years as $y => $label)
                            <option value="{{ $y }}" @selected($year === $y)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Month') }}</label>
                    <select name="month" class="form-select">
                        @foreach ($months as $m => $label)
                            <option value="{{ $m }}" @selected($month === $m)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Employee') }}</label>
                    <select name="employee_id" class="form-select select2">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($employees as $id => $label)
                            <option value="{{ $id }}" @selected((int) request('employee_id') === (int) $id)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Department') }}</label>
                    <select name="department_id" class="form-select select2">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($departments as $id => $label)
                            <option value="{{ $id }}" @selected((int) request('department_id') === (int) $id)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Job Title') }}</label>
                    <select name="job_title_id" class="form-select select2">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($jobTitles as $id => $label)
                            <option value="{{ $id }}" @selected((int) request('job_title_id') === (int) $id)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Payroll Status') }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ __('All') }}</option>
                        @foreach (\App\Enums\PayrollStatusEnum::options() as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <div class="flex-fill">
                        <label class="form-label">{{ __('Net min') }}</label>
                        <input type="number" step="0.01" name="net_min" value="{{ request('net_min') }}"
                            class="form-control">
                    </div>
                    <div class="flex-fill">
                        <label class="form-label">{{ __('Net max') }}</label>
                        <input type="number" step="0.01" name="net_max" value="{{ request('net_max') }}"
                            class="form-control">
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-search me-1"></i>{{ __('Run report') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Totals --}}
    <div class="row mb-4">
        @foreach ([
            ['label' => __('Payrolls'), 'value' => $report['count'], 'icon' => 'ti-file-invoice'],
            ['label' => __('Total Additions'), 'value' => number_format($t['total_additions'], 2), 'icon' => 'ti-plus'],
            ['label' => __('Total Deductions'), 'value' => number_format($t['total_deductions'], 2), 'icon' => 'ti-minus'],
            ['label' => __('Net Salary'), 'value' => number_format($t['net_salary'], 2), 'icon' => 'ti-cash'],
        ] as $card)
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary"><i
                                        class="ti {{ $card['icon'] }}"></i></span>
                            </div>
                            <div>
                                <small class="text-muted d-block">{{ $card['label'] }}</small>
                                <h5 class="mb-0">{{ $card['value'] }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Yearly: per-month breakdown --}}
    @if ($scope === 'yearly' && isset($report['by_month']))
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('Monthly breakdown') }} — {{ $year }}</h5>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Month') }}</th>
                            <th>{{ __('Payrolls') }}</th>
                            <th>{{ __('Additions') }}</th>
                            <th>{{ __('Deductions') }}</th>
                            <th>{{ __('Net Salary') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($report['by_month'] as $m => $row)
                            <tr>
                                <td>{{ $months[$m] }}</td>
                                <td>{{ $row['count'] }}</td>
                                <td>{{ number_format($row['total_additions'], 2) }}</td>
                                <td>{{ number_format($row['total_deductions'], 2) }}</td>
                                <td><strong>{{ number_format($row['net_salary'], 2) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Rows --}}
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Payrolls') }}</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Employee') }}</th>
                        <th>{{ __('Job Title') }}</th>
                        <th>{{ __('Period') }}</th>
                        <th>{{ __('Basic Salary') }}</th>
                        <th>{{ __('Additions') }}</th>
                        <th>{{ __('Deductions') }}</th>
                        <th>{{ __('Net Salary') }}</th>
                        <th>{{ __('Overtime (min)') }}</th>
                        <th>{{ __('Late (min)') }}</th>
                        <th>{{ __('Permission (min)') }}</th>
                        <th>{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($report['rows'] as $row)
                        <tr>
                            <td>{{ $row->employee?->nameLang() ?? '-' }}</td>
                            <td>{{ $row->employee?->jobTitle?->nameLang() ?? '-' }}</td>
                            <td>{{ $row->month }}/{{ $row->year }}</td>
                            <td>{{ number_format((float) $row->basic_salary, 2) }}</td>
                            <td>{{ number_format((float) $row->total_additions, 2) }}</td>
                            <td>{{ number_format((float) $row->total_deductions, 2) }}</td>
                            <td><strong>{{ number_format((float) $row->net_salary, 2) }}</strong></td>
                            <td>{{ $row->total_overtime_minutes }}</td>
                            <td>{{ $row->total_late_minutes }}</td>
                            <td>{{ $row->permission_minutes }}</td>
                            <td>{{ $row->status?->label() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">{{ __('No payrolls for this selection.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
