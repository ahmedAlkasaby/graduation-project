<thead>
    <tr>
        {{-- <th>{{ __('ID') }}</th> --}}
        <th>{{ __('Name') }}</th>
        <th>{{ __('Order No') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
@foreach ($data as $key => $model)
<tr>
    {{-- <td>{{ $model->id }}</td> --}}
    <td>{{ $model->name[$admin_language] }}</td>
    <td>{{ $model->order_id }}</td>
    <td>
        @include('admin.layouts.tables.status',['active'=>$model->active,'ajax_class'=>$model->getTable().'-status','id'=>$model->id])
    </td>
    <td>
        @include('admin.layouts.forms.dropdowns.dropdown', [ 'showEdit' => true])

</tr>
@endforeach
