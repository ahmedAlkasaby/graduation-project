<thead>
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Email') }}</th>
        <th>{{ __('Phone') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Invited At') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
@foreach ($data as $key => $model)
    <tr id="invitation-row-{{ $model->id }}">
        <td>{{ $model->user->full_name }}</td>
        <td>{{ $model->user->email }}</td>
        <td>{{ $model->user->phone }}</td>
        <td class="invitation-status-cell">
            @if ($model->isAccepted())
                <span class="badge bg-success">{{ __('Accepted') }}</span>
            @elseif ($model->isPending())
                <span class="badge bg-warning">{{ __('Pending') }}</span>
            @else
                <span class="badge bg-secondary">{{ __('Revoked') }}</span>
            @endif
        </td>
        <td>{{ $model->invited_at?->toDateTimeString() }}</td>
        <td class="invitation-actions-cell">
            @if (! $model->isAccepted())
                <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-icons">
                        <a href="#" class="dropdown-item invitation-resend" data-id="{{ $model->id }}"
                            data-url="{{ route('admin.user_invitations.resend', $model->id) }}">
                            <i class="ti ti-mail-forward me-1"></i>{{ __('Resend') }}
                        </a>

                        @if (in_array('user_invitations', $admin_delete_models ?? []) && auth()->user()->can('user_invitations.destroy'))
                            <a href="#" class="dropdown-item text-danger invitation-revoke" data-id="{{ $model->id }}"
                                data-url="{{ route('admin.user_invitations.destroy', $model->id) }}">
                                <i class="ti ti-trash me-1"></i>{{ __('Revoke') }}
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
    </tr>
@endforeach
