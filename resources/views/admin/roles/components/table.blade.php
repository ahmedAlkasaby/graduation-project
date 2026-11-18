<thead>
    <tr>
        <th>{{ __('Role') }}</th>
        <th>{{ __('Name') }}</th>
        {{--  <th>{{ __('Permissions') }}</th>  --}}
        <th>{{ __('Web dashboard') }}</th>
        <th>{{ __('Mobile app') }}</th>
        <th>{{ __('Setting') }}</th>

    </tr>
</thead>
<tbody>

@foreach ($data as $key => $model)

<tr>
    <td>{{ userName($model->name) }}</td>
    <td>{{ $model->display_name }}</td>
    {{--  <td>
        @if(!empty($model->permissions))
        @foreach($model->permissions as $v)
        <small class="label bg-red">{{ $v->name }}</small>
        @endforeach
        @endif
    </td>  --}}
    @foreach (['can_login_web', 'can_login_mobile'] as $platformField)
        <td>
            @if ($model->{$platformField})
                <span class="badge bg-label-success">{{ __('Allowed') }}</span>
            @else
                <span class="badge bg-label-secondary">{{ __('Not allowed') }}</span>
            @endif
        </td>
    @endforeach
      <td>
        @include('admin.layouts.forms.dropdowns.dropdown', [  'showEdit' => true])
    </td>
</tr>

@endforeach

