<thead>
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Task') }}</th>
        <th>{{ __('Employee') }}</th>
        <th>{{ __('Minutes') }}</th>
        <th>{{ __('Reason') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Order') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
@foreach ($data as $key => $model)
    <tr>
        <td>{{ $model->name[$admin_language] ?? '' }}</td>
        <td>{{ $model->task?->nameLang() ?? '-' }}</td>
        <td>{{ $model->employee?->nameLang() ?? '-' }}</td>
        <td>{{ $model->minutes ?? '' }}</td>
        <td>{{ $model->reason?->nameLang() ?? '-' }}</td>
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
