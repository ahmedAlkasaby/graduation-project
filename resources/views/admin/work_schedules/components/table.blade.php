<thead>
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Order') }}</th>
    </tr>
</thead>
@foreach ($data as $key => $model)
    <tr>
        <td>{{ $model->name[$admin_language] ?? '' }}</td>
        <td>
            <span class="badge {{ $model->active ? 'bg-label-success' : 'bg-label-secondary' }}">
                {{ $model->active ? __('Active') : __('Inactive') }}
            </span>
        </td>
        <td>{{ $model->order_id }}</td>
    </tr>
@endforeach
