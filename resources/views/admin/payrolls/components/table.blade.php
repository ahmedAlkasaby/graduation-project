<thead>
    <tr>
        <th>{{ __('Employee') }}</th>
        <th>{{ __('Month') }}</th>
        <th>{{ __('Year') }}</th>
        <th>{{ __('Basic Salary') }}</th>
        <th>{{ __('Present Days') }}</th>
        <th>{{ __('Absent Days') }}</th>
        <th>{{ __('Holiday Days') }}</th>
        <th>{{ __('Paid Holiday Days') }}</th>
        <th>{{ __('Unpaid Leave Days') }}</th>
        <th>{{ __('Total Late Minutes') }}</th>
        <th>{{ __('Total Overtime Minutes') }}</th>
        <th>{{ __('Total Additions') }}</th>
        <th>{{ __('Total Deductions') }}</th>
        <th>{{ __('Net Salary') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Finalize') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
@foreach ($data as $key => $model)
    <tr>
        <td>{{ $model->employee?->nameLang() ?? '-' }}</td>
        <td>{{ $model->month ?? '' }}</td>
        <td>{{ $model->year ?? '' }}</td>
        <td>{{ $model->basic_salary ?? '' }}</td>
        <td>{{ $model->present_days ?? '' }}</td>
        <td>{{ $model->absent_days ?? '' }}</td>
        <td>{{ $model->holiday_days ?? '' }}</td>
        <td>{{ $model->paid_holiday_days ?? '' }}</td>
        <td>{{ $model->unpaid_leave_days ?? '' }}</td>
        <td>{{ $model->total_late_minutes ?? '' }}</td>
        <td>{{ $model->total_overtime_minutes ?? '' }}</td>
        <td>{{ $model->total_additions ?? '' }}</td>
        <td>{{ $model->total_deductions ?? '' }}</td>
        <td><strong>{{ $model->net_salary ?? '' }}</strong></td>
        <td>{{ $model->status?->label() ?? '' }}</td>
        <td>
            @if ($model->isFinalized())
                <span class="badge bg-label-success">{{ __('Finalized') }}</span>
            @else
                <form action="{{ route('admin.payrolls.finalize', $model->id) }}" method="POST"
                    onsubmit="return confirm('{{ __('Finalize this payroll? It can no longer be recalculated.') }}');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('Finalize') }}</button>
                </form>
            @endif
        </td>
        <td>
            @include('admin.layouts.forms.dropdowns.dropdown', ['show' => true, 'showEdit' => true])
        </td>
    </tr>
@endforeach
