<thead>
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Project') }}</th>
        <th>{{ __('Folder') }}</th>
        <th>{{ __('Title') }}</th>
        <th>{{ __('Priority') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Estimated Minutes') }}</th>
        <th>{{ __('Time Tracked Minutes') }}</th>
        <th>{{ __('Assignees') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Order') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
@foreach ($data as $key => $model)
    <tr>
        <td>{{ $model->name[$admin_language] ?? '' }}</td>
        <td>{{ $model->project?->nameLang() ?? '-' }}</td>
        <td>{{ $model->folder?->nameLang() ?? '-' }}</td>
        <td>{{ $model->title ?? '' }}</td>
        <td>{{ $model->priority ?? '' }}</td>
        <td>
            @if ($model->taskStatus)
                <span class="d-inline-flex align-items-center">
                    <span class="rounded-circle me-1" style="width: 8px; height: 8px; background: {{ $model->taskStatus->color }}"></span>
                    {{ $model->taskStatus->name }}
                </span>
            @else
                -
            @endif
        </td>
        <td>{{ $model->estimated_minutes ?? '' }}</td>
        <td>{{ $model->time_tracked_minutes ?? '' }}</td>
        <td>{{ $model->assigneeNames() ?? '-' }}</td>
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
