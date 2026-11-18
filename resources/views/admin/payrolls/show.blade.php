@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])

@section('title')
    {{ __('Payroll Details') }}
@stop

@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => route('admin.payrolls.index')])
@stop

@section('content')
    @include('admin.errors.alerts')

    {{-- Header card: employee info + status + key dates --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h4 class="mb-1">{{ $payroll->employee?->nameLang() }}</h4>

                    @php
                        $statusColor = match ($payroll->status?->value) {
                            'paid'      => 'success',
                            'processed' => 'info',
                            default     => 'secondary',
                        };
                    @endphp
                    <span class="badge bg-label-{{ $statusColor }} mb-3">
                        {{ $payroll->status?->label() }}
                    </span>

                    <table class="table table-sm table-borderless mb-0" style="max-width:480px;">
                        <tbody>
                            <tr>
                                <th class="text-muted fw-normal ps-0" style="width:160px;">{{ __('Employee') }}</th>
                                <td>
                                    {{ $payroll->employee?->code }}
                                    @if ($payroll->employee?->jobTitle)
                                        &middot; {{ $payroll->employee->jobTitle->nameLang() }}
                                    @endif
                                    @if ($payroll->employee?->department)
                                        &middot; {{ $payroll->employee->department->nameLang() }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal ps-0">{{ __('Pay period') }}</th>
                                <td>
                                    {{ $payroll->period_start?->format('d') }}–{{ $payroll->period_end?->format('d') }}
                                    {{ $payroll->period_start?->translatedFormat('F Y') }}
                                </td>
                            </tr>
                            @if ($payroll->paid_at)
                                <tr>
                                    <th class="text-muted fw-normal ps-0">{{ __('Payment date') }}</th>
                                    <td>{{ $payroll->paid_at->translatedFormat('d F Y') }}</td>
                                </tr>
                            @endif
                            @if ($payroll->notes)
                                <tr>
                                    <th class="text-muted fw-normal ps-0">{{ __('Notes') }}</th>
                                    <td>{{ $payroll->notes }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-column gap-2">
                    {{-- Download payslip (print-friendly) --}}
                    <a href="{{ route('admin.payrolls.show', $payroll->id) }}?print=1"
                       target="_blank"
                       class="btn btn-outline-secondary">
                        <i class="ti ti-download me-1"></i>{{ __('Download Payslip') }}
                    </a>

                    @if (! $payroll->isFinalized())
                        <form action="{{ route('admin.payrolls.finalize', $payroll->id) }}" method="POST"
                              onsubmit="return confirm('{{ __('Finalize this payroll? It can no longer be recalculated.') }}');">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-lock me-1"></i>{{ __('Finalize') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Earnings & Deductions --}}
    <div class="row">
        {{-- Earnings --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Earnings') }}</h5>
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Description') }}</th>
                                <th class="text-end text-muted small">
                                    {{ __('Amount') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Basic salary always first --}}
                            <tr>
                                <td>{{ __('Basic Salary') }}</td>
                                <td class="text-end">{{ number_format((float) $payroll->basic_salary, 2) }}</td>
                            </tr>
                            @foreach ($earnings as $item)
                                <tr>
                                    <td>
                                        {{ $item->label() }}
                                        @if ($payroll->total_overtime_minutes > 0 && ($item->category ?? '') === 'overtime')
                                            <span class="text-muted small">
                                                &middot; {{ $payroll->total_overtime_minutes }} {{ __('min') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ number_format((float) $item->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <th>{{ __('Gross Salary') }}</th>
                                <th class="text-end">
                                    {{ number_format((float) $payroll->basic_salary + (float) $payroll->total_additions, 2) }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Deductions --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Deductions') }}</h5>
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Description') }}</th>
                                <th class="text-end">{{ __('Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($deductions as $item)
                                <tr>
                                    <td>{{ $item->label() }}</td>
                                    <td class="text-end">{{ number_format((float) $item->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-muted text-center py-3">{{ __('No deductions') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <th>{{ __('Total Deductions') }}</th>
                                <th class="text-end">{{ number_format((float) $payroll->total_deductions, 2) }}</th>
                            </tr>
                            <tr>
                                <th>{{ __('Net Salary') }}</th>
                                <th class="text-end fs-5 text-primary">
                                    {{ number_format((float) $payroll->net_salary, 2) }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Attendance summary --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">{{ __('Attendance Summary') }}</h5>
            <div class="row text-center g-3">
                <div class="col-6 col-sm-4 col-md-2">
                    <div class="border rounded p-3">
                        <div class="fs-4 fw-bold">{{ $payroll->present_days ?? 0 }}</div>
                        <div class="text-muted small">{{ __('Present days') }}</div>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <div class="border rounded p-3">
                        <div class="fs-4 fw-bold text-danger">{{ $payroll->absent_days ?? 0 }}</div>
                        <div class="text-muted small">{{ __('Absent days') }}</div>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <div class="border rounded p-3">
                        <div class="fs-4 fw-bold text-info">{{ $payroll->holiday_days ?? 0 }}</div>
                        <div class="text-muted small">{{ __('Holiday days') }}</div>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <div class="border rounded p-3">
                        <div class="fs-4 fw-bold text-warning">{{ $payroll->unpaid_leave_days ?? 0 }}</div>
                        <div class="text-muted small">{{ __('Unpaid leave') }}</div>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <div class="border rounded p-3">
                        <div class="fs-4 fw-bold text-success">{{ $payroll->total_overtime_minutes ?? 0 }}</div>
                        <div class="text-muted small">{{ __('Overtime (min)') }}</div>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <div class="border rounded p-3">
                        <div class="fs-4 fw-bold text-danger">{{ $payroll->total_late_minutes ?? 0 }}</div>
                        <div class="text-muted small">{{ __('Late (min)') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="d-flex gap-2 mb-4">
        @can('payrolls.edit')
            <a href="{{ route('admin.payrolls.edit', $payroll->id) }}" class="btn btn-outline-primary">
                <i class="ti ti-pencil me-1"></i>{{ __('Edit') }}
            </a>
        @endcan
        @can('payrolls.destroy')
            <form action="{{ route('admin.payrolls.destroy', $payroll->id) }}" method="POST"
                  onsubmit="return confirm('{{ __('Are you sure you want to delete this payroll?') }}');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="ti ti-trash me-1"></i>{{ __('Delete') }}
                </button>
            </form>
        @endcan
    </div>
@stop
