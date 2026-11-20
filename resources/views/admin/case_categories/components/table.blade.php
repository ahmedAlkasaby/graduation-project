<thead class="border-top">
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Assistances Count') }}</th>
        <th>{{ __('Order ID') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
<tbody>
@foreach ($data as $model)
    <tr>
        <td>{{ $model->nameLang($admin_language) }}</td>
        <td>{{ $model->assistances_count }}</td>
        <td>{{ $model->order_id }}</td>
        <td>
            @can('case_categories.status')
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
                'showDelete' => true,
            ])
        </td>
    </tr>
@endforeach
</tbody>
