<thead>
    <tr>
        <th>{{ __('Employee') }}</th>
        <th>{{ __('Holiday Type') }}</th>
        <th>{{ __('Start Date') }}</th>
        <th>{{ __('End Date') }}</th>
        <th>{{ __('Days') }}</th>
        <th>{{ __('Reason') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Reviewed By') }}</th>
        <th>{{ __('Actions') }}</th>
        <th>{{ __('Active') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
@foreach ($data as $key => $model)
    <tr>
        <td>{{ $model->employee?->nameLang() ?? '-' }}</td>
        <td>{{ $model->type?->label() ?? '-' }}</td>
        <td>{{ $model->start_date?->format('Y-m-d') ?? '-' }}</td>
        <td>{{ $model->end_date?->format('Y-m-d') ?? '-' }}</td>
        <td>{{ $model->days ?? '' }}</td>
        <td>{{ $model->reason?->nameLang() ?? '-' }}</td>
        <td>
            @php
                $badgeClass = match($model->status?->value ?? $model->status) {
                    'pending' => 'bg-label-warning',
                    'approved' => 'bg-label-success',
                    'rejected' => 'bg-label-danger',
                    'cancelled' => 'bg-label-secondary',
                    default => 'bg-label-info'
                };
            @endphp
            <span class="badge {{ $badgeClass }}">
                {{ $model->status?->label() ?? __('Unknown') }}
            </span>
        </td>
        <td>{{ $model->reviewer?->name ?? '-' }}</td>
        <td>
            @if($model->status?->value === 'pending' || $model->status === 'pending')
                <div class="d-flex gap-2">
                    <button type="button"
                            class="btn btn-sm btn-success approve-btn"
                            data-id="{{ $model->id }}"
                            data-url="{{ route('admin.holiday_requests.approve', $model->id) }}">
                        <i class="ti ti-check"></i> {{ __('Approve') }}
                    </button>

                    <button type="button"
                            class="btn btn-sm btn-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#rejectModal{{ $model->id }}">
                        <i class="ti ti-x"></i> {{ __('Reject') }}
                    </button>
                </div>

                <!-- Reject Modal -->
                <div class="modal fade" id="rejectModal{{ $model->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form class="reject-form" data-id="{{ $model->id }}" data-url="{{ route('admin.holiday_requests.reject', $model->id) }}">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ __('Reject Holiday Request') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Rejection Reason') }} <span class="text-danger">*</span></label>
                                        <select name="rejection_reason_id" class="form-select" required>
                                            <option value="">{{ __('Select Reason') }}</option>
                                            @foreach(\App\Models\RejectReason::where('active', 1)->get() as $reason)
                                                <option value="{{ $reason->id }}">{{ $reason->nameLang() }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                    <button type="submit" class="btn btn-danger">{{ __('Reject Request') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <span class="badge bg-label-secondary">{{ __('No Actions') }}</span>
            @endif
        </td>
        <td>
            @include('admin.layouts.tables.status', [
                'active' => $model->active,
                'id' => $model->id,
                'route' => route('admin.' . $model->getTable() . '.status'),
            ])
        </td>
        <td>
            @include('admin.layouts.forms.dropdowns.dropdown', ['showEdit' => true])
        </td>
    </tr>
@endforeach
