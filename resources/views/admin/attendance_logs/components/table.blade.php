<thead>
    <tr>
        <th>{{ __('Employee') }}</th>
        <th>{{ __('Type') }}</th>
        <th>{{ __('Qr Token') }}</th>
        <th>{{ __('Latitude') }}</th>
        <th>{{ __('Longitude') }}</th>
        <th>{{ __('Distance From Company') }}</th>
        <th>{{ __('Device Identifier') }}</th>
        <th>{{ __('Source') }}</th>
        <th>{{ __('Daily Attendance') }}</th>
        <th>{{ __('Ip Address') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Order') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
@foreach ($data as $key => $model)
    <tr>
        <td>{{ $model->employee?->nameLang() ?? '-' }}</td>
        <td>{{ $model->type ?? '' }}</td>
        <td>{{ $model->qr_token ?? '' }}</td>
        <td>{{ $model->latitude ?? '' }}</td>
        <td>{{ $model->longitude ?? '' }}</td>
        <td>{{ $model->distance_from_company ?? '' }}</td>
        <td>{{ $model->device_identifier ?? '' }}</td>
        <td>{{ $model->source ?? '' }}</td>
        <td>{{ $model->dailyAttendance?->attendance_date?->toDateString() ?? '-' }}</td>
        <td>{{ $model->ip_address ?? '' }}</td>
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
