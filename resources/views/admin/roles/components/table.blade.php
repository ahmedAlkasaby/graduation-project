<thead class="border-top">
    <tr>
        <th>{{ __('Role') }}</th>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Web dashboard') }}</th>
        <th>{{ __('Mobile app') }}</th>
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
        @foreach (['can_login_web', 'can_login_mobile'] as $platformField)
            <td>
                @if ($model->{$platformField})
                    <span class="badge bg-label-success">{{ __('Allowed') }}</span>
                @else
                    <span class="badge bg-label-secondary">{{ __('Not allowed') }}</span>
                @endif
            </td>
        @endforeach
        <td><span class="badge bg-label-info">{{ $model->users_count }}</span></td>
        <td><span class="badge bg-label-primary">{{ $model->permissions_count }}</span></td>
        <td>
            {{-- A seeded role is edited only by its holders and never deleted; a role still held by users would orphan them. --}}
            @include('admin.layouts.forms.dropdowns.dropdown', [
                'show' => true,
                'showEdit' => auth()->user()->can('update', $model),
                'showDelete' => ! in_array($model->name, $systemRoles, true) && (int) $model->users_count === 0,
            ])
        </td>
    </tr>
@endforeach
</tbody>
