<thead class="border-top">
    <tr>
        <th>{{ __('Beneficiary') }}</th>
        <th>{{ __('National ID') }}</th>
        <th>{{ __('Category') }}</th>
        <th>{{ __('Volunteer') }}</th>
        <th>{{ __('Assistances') }}</th>
        <th>{{ __('Created At') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
<tbody>
@forelse ($data as $model)
    <tr>
        <td>
            <div class="fw-medium">{{ $model->full_name }}</div>
            <small class="text-muted">{{ $model->phone ?? '—' }}</small>
        </td>
        <td>{{ $model->national_id ?? '—' }}</td>
        <td><span class="badge bg-label-info">{{ $model->category?->nameLang($admin_language) }}</span></td>
        <td>{{ $model->volunteer?->full_name ?? __('Unassigned') }}</td>
        <td>{{ $model->assistances_count }}</td>
        <td>{{ $model->created_at?->toDateString() }}</td>
        <td>
            @include('admin.layouts.forms.dropdowns.dropdown', [
                'show' => true,
                'showEdit' => true,
            ])
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center text-muted py-4">{{ __('No cases found') }}</td>
    </tr>
@endforelse
</tbody>
