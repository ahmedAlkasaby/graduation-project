<thead>
    <tr>
        <th>{{ __('Employee') }}</th>
        <th>{{ __('From Time') }}</th>
        <th>{{ __('To Time') }}</th>
        <th>{{ __('Minutes') }}</th>
        <th>{{ __('Period') }}</th>
        <th>{{ __('Reason') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Rejection Reason') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Order') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
@foreach ($data as $key => $model)
    <tr>
        <td>{{ $model->employee?->nameLang() ?? '-' }}</td>
        <td>{{ $model->from_time ?? '' }}</td>
        <td>{{ $model->to_time ?? '' }}</td>
        <td>{{ $model->minutes ?? '' }}</td>
        <td>{{ $model->period ?? '' }}</td>
        <td>{{ $model->reason?->nameLang() ?? '-' }}</td>
        <td>{{ $model->status ?? '' }}</td>
        <td>{{ $model->rejectionReason?->nameLang() ?? '-' }}</td>
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
