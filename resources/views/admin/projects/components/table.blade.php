<thead>
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Deadline') }}</th>
        <th>{{ __('Progress') }}</th>
        <th>{{ __('Owner') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Order') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
@foreach ($data as $key => $model)
    <tr>
        <td>{{ $model->name[$admin_language] ?? '' }}</td>
        <td>{{ $model->status ?? '' }}</td>
        <td>{{ $model->deadline?->format('Y-m-d') ?? '-' }}</td>
        <td>{{ $model->progress }}%</td>
        <td>{{ $model->owner?->nameLang() ?? '-' }}</td>
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
