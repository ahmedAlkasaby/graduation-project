<thead class="border-top">
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Email') }}</th>
        <th>{{ __('Phone') }}</th>
        <th>{{ __('Image') }}</th>
        <th>{{ __('Type') }}</th>
        <th>{{ __('Roles') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
<tbody>
@foreach ($data as $model)
    <tr>
        <td>{{ $model->full_name }}</td>
        <td>{{ $model->email }}</td>
        <td>{{ $model->phone }}</td>
        <td>
            @if ($model->image && upload_exists($model->image))
                <img src="{{ upload_url($model->image) }}" class="table-img rounded" alt="{{ $model->full_name }}">
            @endif
        </td>
        <td>{{ userName($model->type) }}</td>
        <td>
            @foreach ($model->roles as $role)
                <span class="badge bg-label-primary">{{ $role->label() }}</span>
            @endforeach
        </td>
        <td>
            @if ($model->trashed())
                <span class="badge bg-label-danger">{{ __('Deleted') }}</span>
            @elseif (auth()->user()->can('users.status') && ! $model->is(auth()->user()))
                @include('admin.layouts.tables.status', [
                    'active' => $model->active,
                    'ajax_class' => $model->getTable() . '-status',
                    'id' => $model->id,
                ])
            @else
                {{ statusName((int) $model->active) }}
            @endif
        </td>
        <td>
            @include('admin.layouts.forms.dropdowns.dropdown', [
                'show' => true,
                'showEdit' => true,
                'showDelete' => ! $model->is(auth()->user()),
                'showRestore' => true,
            ])
        </td>
    </tr>
@endforeach
</tbody>
