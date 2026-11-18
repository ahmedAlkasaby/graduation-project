<thead class="border-top">
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Country') }}</th>
        <th>{{ __('Order ID') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
<tbody>
@foreach ($data as $key => $model)
    <tr>
        <td>{{ $model->nameLang($admin_language) }}</td>
        <td>{{ optional($model->country)->nameLang($admin_language) }}</td>
        <td>{{ $model->order_id }}</td>
        <td>
            @include('admin.layouts.tables.status', [
                'active' => $model->active,
                'ajax_class' => $model->getTable() . '-status',
                'id' => $model->id,
            ])
        </td>
        <td>
            @include('admin.layouts.forms.dropdowns.dropdown', [
                'showEdit' => true,
                'showDelete' => true,
            ])
        </td>
    </tr>
@endforeach
</tbody>
