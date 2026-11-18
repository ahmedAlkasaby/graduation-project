<thead>
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Payroll') }}</th>
        <th>{{ __('Type') }}</th>
        <th>{{ __('Category') }}</th>
        <th>{{ __('Amount') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
@foreach ($data as $key => $model)
    <tr>
        <td>{{ $model->label() }}</td>
        <td>{{ $model->payroll?->nameLang() ?? '-' }}</td>
        <td>{{ $model->type ?? '' }}</td>
        <td>{{ $model->category ?? '' }}</td>
        <td>{{ $model->amount ?? '' }}</td>
        <td>
            @include('admin.layouts.forms.dropdowns.dropdown', ['showEdit' => true])
        </td>
    </tr>
@endforeach
