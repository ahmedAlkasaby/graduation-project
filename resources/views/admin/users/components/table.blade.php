<thead class="border-top">
    <tr>

        <th>{{ __('Name') }}</th>
        <th>{{ __('Email') }}</th>
        <th>{{ __('Phone') }}</th>
        <th>{{ __('Image') }}</th>
        <th>{{ __('Type') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Setting') }}</th>
    </tr>
</thead>
@foreach ($data as $key => $model)
    <tr class="odd">
        <td>{{ $model->name }}</td>
        <td>{{ $model->email }}</td>
        <td>{{ $model->phone }}</td>
        <td>
            @if ($model->image && upload_exists($model->image))
                <div class="gallery">
                    <a href="{{ upload_url($model->image) }}" data-lg-zoom="true">
                        <img src="{{ upload_url($model->image) }}" class="table-img">
                    </a>
                </div>
            @else
                <!-- Optional: show a placeholder or leave it blank -->
                {{-- <span>{{ __('No Image') }}</span> --}}
            @endif
        </td>
        <td>{{ userName($model->type) }}</td>
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
                'showRestore' => true,
            ])
        </td>    </tr>
@endforeach
</tbody>
