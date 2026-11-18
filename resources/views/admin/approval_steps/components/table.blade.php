<thead>
    <tr>
        <th>{{ __('Request Type') }}</th>
        <th>{{ __('Request') }}</th>
        <th>{{ __('Step Order') }}</th>
        <th>{{ __('Approver') }}</th>
        <th>{{ __('Approver Role') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
@foreach ($data as $key => $model)
    <tr>
        <td>{{ $model->request_type ?? '' }}</td>
        <td>{{ $model->request?->nameLang() ?? '-' }}</td>
        <td>{{ $model->step_order ?? '' }}</td>
        <td>{{ $model->approver?->nameLang() ?? '-' }}</td>
        <td>{{ $model->approver_role ?? '' }}</td>
        <td>{{ $model->status ?? '' }}</td>
        <td>
            @include('admin.layouts.forms.dropdowns.dropdown', ['showEdit' => true])
        </td>
    </tr>
@endforeach
