<thead>
    <tr>
        <th>{{ __('Employee') }}</th>
        <th>{{ __('Device Name') }}</th>
        <th>{{ __('Platform') }}</th>
        <th>{{ __('Installation ID') }}</th>
        <th>{{ __('Credential Type') }}</th>
        <th>{{ __('Requested At') }}</th>
        <th>{{ __('Expires At') }}</th>
        <th>{{ __('Actions') }}</th>
    </tr>
</thead>
<tbody>
@forelse ($data as $key => $device)
    <tr id="device-row-{{ $device->id }}" class="{{ $device->isExpired() ? 'table-warning' : '' }}">
        <td>
            <div class="d-flex align-items-center">
                <div>
                    <strong>{{ $device->user?->full_name }}</strong>
                    @if($device->user?->employee)
                        <br><small class="text-muted">{{ $device->user->employee->job_title ?? '' }}</small>
                    @endif
                </div>
            </div>
        </td>
        <td>
            {{ $device->device_name ?? '-' }}
            @if($device->imei)
                <br><small class="text-muted">IMEI: {{ $device->imei }}</small>
            @endif
        </td>
        <td>
            <span class="badge bg-info">{{ $device->getPlatformLabel() }}</span>
        </td>
        <td>
            <code class="small">{{ Str::limit($device->installation_id, 20) }}</code>
        </td>
        <td>
            @if($device->credential_type)
                <small class="text-muted">{{ str_replace('_', ' ', ucwords($device->credential_type, '_')) }}</small>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            {{ $device->created_at?->diffForHumans() }}
            <br><small class="text-muted">{{ $device->created_at?->format('Y-m-d H:i') }}</small>
        </td>
        <td>
            @if($device->isExpired())
                <span class="badge bg-danger">{{ __('Expired') }}</span>
                <br><small class="text-muted">{{ $device->expires_at?->format('Y-m-d H:i') }}</small>
            @else
                {{ $device->expires_at?->diffForHumans() }}
                <br><small class="text-muted">{{ $device->expires_at?->format('Y-m-d H:i') }}</small>
            @endif
        </td>
        <td>
            <div class="btn-group" role="group">
                <a href="{{ route('admin.devices.show', $device->id) }}" 
                   class="btn btn-sm btn-outline-primary" 
                   title="{{ __('View Details') }}">
                    <i class="fas fa-eye"></i>
                </a>
                @if(!$device->isExpired())
                    <button type="button" 
                            class="btn btn-sm btn-success btn-approve-device"
                            data-id="{{ $device->id }}"
                            title="{{ __('Approve') }}">
                        <i class="fas fa-check"></i>
                    </button>
                    <button type="button" 
                            class="btn btn-sm btn-danger btn-reject-device"
                            data-id="{{ $device->id }}"
                            title="{{ __('Reject') }}">
                        <i class="fas fa-times"></i>
                    </button>
                @else
                    <span class="text-muted small">{{ __('Expired') }}</span>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center py-4">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <p class="text-muted">{{ __('No pending device registrations') }}</p>
        </td>
    </tr>
@endforelse
</tbody>
