<thead>
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Email') }}</th>
        <th>{{ __('Employee Code') }}</th>
        <th>{{ __('Employment Type') }}</th>
        <th>{{ __('Phone') }}</th>
        <th>{{ __('Religion') }}</th>
        <th>{{ __('National Id') }}</th>
        <th>{{ __('Job Title') }}</th>
        <th>{{ __('Manager') }}</th>
        <th>{{ __('Basic Salary') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
@foreach ($data as $key => $model)
    <tr>
        <td>{{ $model->nameLang() ?? '' }}</td>
        <td>{{ $model->user?->email ?? '-' }}</td>
        <td>{{ $model->code ?? '' }}</td>
        <td>{{ $model->type?->label() ?? '-' }}</td>
        <td>{{ $model->user?->phone ?? '-' }}</td>
        <td>{{ $model->religion?->label() ?? '-' }}</td>
        <td>{{ $model->national_id ?? '-' }}</td>
        <td>{{ $model->jobTitle?->nameLang() ?? '-' }}</td>
        <td>{{ $model->manager?->nameLang() ?? '-' }}</td>
        <td>{{ $model->basic_salary ?? '' }}</td>
        <td>
            @if ($model->user)
                @include('admin.layouts.tables.status', [
                    'active' => $model->user->active,
                    'id' => $model->user_id,
                    'route' => route('admin.users.status'),
                ])
            @endif
        </td>
        <td>
            @include('admin.layouts.forms.dropdowns.dropdown', ['showEdit' => true])
        </td>
    </tr>
@endforeach
