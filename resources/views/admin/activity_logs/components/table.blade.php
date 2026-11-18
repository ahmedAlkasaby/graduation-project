<thead class="border-top">
    <tr>
        <th>#</th>
        <th>{{ __('User') }}</th>
        <th>{{ __('Action') }}</th>
        <th>{{ __('Model') }}</th>
        <th>{{ __('Route') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Type') }}</th>
        <th>{{ __('Device') }}</th>
        <th>{{ __('Date') }}</th>
    </tr>
</thead>
<tbody>
    @foreach ($data as $model)
        <tr>
            <td>{{ $model->id }}</td>
            <td>{{ optional($model->user)->name ?? '-' }}</td>
            <td>
                <span class="badge bg-label-{{ logActionColor($model->action) }}">
                    {{ logAction($model->action) }}
                </span>
            </td>
            <td>
                {{ getModuleTableModelName($model->activity_loggable_type) }}
                @if ($model->activity_loggable_id)
                    <small class="text-muted">#{{ $model->activity_loggable_id }}</small>
                @endif
            </td>
            <td><small>{{ $model->route }}</small></td>
            <td>{{ $model->status }}</td>
            <td>{{ logTypeOptions()[$model->type] ?? $model->type }}</td>
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
