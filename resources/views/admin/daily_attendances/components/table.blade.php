<thead>
    <tr>
        <th>{{ __('Employee') }}</th>
        <th>{{ __('Scheduled Work Minutes') }}</th>
        <th>{{ __('Scheduled Break Minutes') }}</th>
        <th>{{ __('Latest Check In') }}</th>
        <th>{{ __('Permission Requests') }}</th>
        <th>{{ __('Break Minutes') }}</th>
        <th>{{ __('Worked Minutes') }}</th>
        <th>{{ __('Late Minutes') }}</th>
        <th>{{ __('Early Leave Minutes') }}</th>
        <th>{{ __('Overtime Minutes') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Order') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
@foreach ($data as $key => $model)
    <tr>
        <td>{{ $model->employee?->nameLang() ?? '-' }}</td>
        <td>{{ $model->scheduled_work_minutes ?? '' }}</td>
        <td>{{ $model->scheduled_break_minutes ?? '' }}</td>
        <td>{{ $model->latest_check_in ?? '' }}</td>
        <td>{{ $model->permissionRequests->map->label()->join(', ') ?: '-' }}</td>
        <td>{{ $model->break_minutes ?? '' }}</td>
        <td>{{ $model->worked_minutes ?? '' }}</td>
        <td>{{ $model->late_minutes ?? '' }}</td>
        <td>{{ $model->early_leave_minutes ?? '' }}</td>
        <td>{{ $model->overtime_minutes ?? '' }}</td>
        <td>{{ \App\Enums\DailyAttendanceStatusEnum::labelFor($model->status) }}</td>
        <td>
            @include('admin.layouts.tables.status', [
                'active' => $model->active,
                'id' => $model->id,
                'route' => route('admin.' . $model->getTable() . '.status'),
            ])
        </td>
        <td>{{ $model->order_id }}</td>
        <td>
            @include('admin.layouts.forms.dropdowns.dropdown', ['showEdit' => true])
        </td>
    </tr>
@endforeach
