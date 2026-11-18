<thead>
    <tr>
        <th>{{ __('Employee') }}</th>
        <th>{{ __('Device') }}</th>
        <th>{{ __('Type') }}</th>
        <th>{{ __('State') }}</th>
        <th>{{ __('Requested At') }}</th>
        <th>{{ __('Actions') }}</th>
    </tr>
</thead>
@foreach ($data as $key => $model)
    <tr id="device-row-{{ $model->id }}">
        <td>{{ $model->user?->full_name }}</td>
        <td>{{ $model->device_name ?? '-' }}</td>
        <td>{{ $model->type }}</td>
        <td><span class="badge bg-{{ $model->state?->color() ?? 'secondary' }} device-state">{{ $model->state?->label() }}</span></td>
        <td>{{ $model->created_at?->toDateTimeString() }}</td>
        <td class="device-actions">
            @if ($model->isPending())
                <button type="button" class="btn btn-sm btn-success device-action"
                    data-url="{{ route('admin.devices.approve', $model->id) }}"
                    data-id="{{ $model->id }}">{{ __('Approve') }}</button>
                {{-- Rejecting needs a reason from reject_reasons, chosen on the details page. --}}
                <a href="{{ route('admin.devices.show', $model->id) }}" class="btn btn-sm btn-danger">{{ __('Reject') }}</a>
                <button type="button" class="btn btn-sm btn-secondary device-action"
                    data-url="{{ route('admin.devices.cancel', $model->id) }}"
                    data-id="{{ $model->id }}">{{ __('Cancel') }}</button>
            @else
                <a href="{{ route('admin.devices.show', $model->id) }}" class="btn btn-sm btn-outline-primary">{{ __('Show') }}</a>
            @endif
        </td>
    </tr>
@endforeach
