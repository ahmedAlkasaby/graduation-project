<thead class="border-top">
    <tr>
        <th>{{ __('Title') }}</th>
        <th>{{ __('Beneficiary') }}</th>
        <th>{{ __('Category') }}</th>
        <th>{{ __('Type') }}</th>
        <th>{{ __('Amount') }}</th>
        <th>{{ __('Installments') }}</th>
        <th>{{ __('Next Due') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Active') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
<tbody>
@foreach ($data as $model)
    <tr>
        <td class="fw-medium">{{ $model->titleLang($admin_language) }}</td>
        <td>
            <div>{{ $model->charityCase?->full_name }}</div>
            <small class="text-muted">{{ $model->charityCase?->national_id ?? $model->charityCase?->phone }}</small>
        </td>
        <td><span class="badge bg-label-info">{{ $model->charityCase?->category?->nameLang($admin_language) }}</span></td>
        <td>@include('admin.case_assistances.components.type-badge', ['assistance' => $model])</td>
        <td>@include('admin.case_assistances.components.amount', ['assistance' => $model])</td>
        <td>{{ $model->paid_installments_count }} / {{ $model->installments_count }} {{ __('paid') }}</td>
        <td>@include('admin.case_assistances.components.next-due', ['assistance' => $model])</td>
        <td><span class="badge bg-label-{{ $model->status()->color() }}">{{ $model->status()->label() }}</span></td>
        <td>
            @can('case_assistances.status')
                @include('admin.layouts.tables.status', [
                    'active' => $model->active,
                    'ajax_class' => $model->getTable() . '-status',
                    'id' => $model->id,
                ])
            @else
                {{ statusName((int) $model->active) }}
            @endcan
        </td>
        <td>
            @include('admin.layouts.forms.dropdowns.dropdown', [
                'show' => true,
                'showEdit' => true,
            ])
        </td>
    </tr>
@endforeach
</tbody>
