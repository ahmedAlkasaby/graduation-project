<thead class="border-top">
    <tr>
        <th>#</th>
        <th>{{ __('User') }}</th>
        <th>{{ __('Model') }}</th>
        <th>{{ __('Resource ID') }}</th>
        <th>{{ __('Field') }}</th>
        <th>{{ __('Old Value') }}</th>
        <th>{{ __('New Value') }}</th>
        <th>{{ __('Device') }}</th>
        <th>{{ __('Date') }}</th>
    </tr>
</thead>
<tbody>
    @foreach ($data as $model)
        <tr>
            <td>{{ $model->id }}</td>
            <td>{{ optional($model->user)->full_name ?? '-' }}</td>
            <td>{{ getModuleTableModelName($model->loggable_type) }}</td>
            <td>{{ $model->loggable_id }}</td>
            <td><code>{{ $model->key }}</code></td>
            <td><small class="text-danger">{{ Str::limit($model->value_old, 80) }}</small></td>
            <td><small class="text-success">{{ Str::limit($model->value, 80) }}</small></td>
            <td>
                @if ($model->userDevice)
                    {{ $model->userDevice->title() }}
                    <br><small class="text-muted">{{ $model->userDevice->ip }}</small>
                @else
                    -
                @endif
            </td>
            <td>{{ optional($model->created_at)->format('Y-m-d H:i') }}</td>
        </tr>
    @endforeach
</tbody>
