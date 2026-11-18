<thead>
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Start Date') }}</th>
        <th>{{ __('End Date') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Order') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
@foreach ($data as $key => $model)
    <tr>
        <td>{{ $model->name[$admin_language] ?? '' }}</td>
        <td>{{ $model->date_start?->toDateString() }}</td>
        <td>{{ $model->date_end?->toDateString() }}</td>
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
