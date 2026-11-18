<thead>
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Title') }}</th>
        <th>{{ __('Category') }}</th>
        <th>{{ __('Audience') }}</th>
        <th>{{ __('Department') }}</th>
        <th>{{ __('Employee') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Order') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
@foreach ($data as $key => $model)
    <tr>
        <td>{{ $model->name[$admin_language] ?? '' }}</td>
        <td>{{ $model->title ?? '' }}</td>
        <td>{{ $model->category ?? '' }}</td>
        <td>{{ $model->audience ?? '' }}</td>
        <td>{{ $model->department?->nameLang() ?? '-' }}</td>
        <td>{{ $model->employee?->nameLang() ?? '-' }}</td>
        <td>{{ $model->status ?? '' }}</td>
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
