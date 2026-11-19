<thead class="border-top">
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Display Name') }}</th>
        <th>{{ __('Users') }}</th>
        <th>{{ __('Permissions') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
<tbody>
@foreach ($data as $model)
    <tr>
        <td><code>{{ $model->name }}</code></td>
        <td>{{ $model->display_name }}</td>
        <td><span class="badge bg-label-info">{{ $model->users_count }}</span></td>
        <td><span class="badge bg-label-primary">{{ $model->permissions_count }}</span></td>
        <td>
            {{-- Seeded roles come back on every seed, and a role still held by users would orphan them. --}}
            @include('admin.layouts.forms.dropdowns.dropdown', [
                'show' => true,
                'showEdit' => true,
                'showDelete' => ! in_array($model->name, $systemRoles, true) && (int) $model->users_count === 0,
            ])
        </td>
    </tr>
@endforeach
</tbody>
