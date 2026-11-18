@extends('admin.layouts.app')
@section('title') {{ __('Show Role') }}
@stop
@section('head_content')
@include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.roles.index')])
@stop
@section('content')

<div class="row">
    <div class="box">
        <div class="box-body">
            <div class="form-group">
                <label>{{ __('Name') }}</label>
                {{ $role->name }}
            </div>
            <div class="form-group">
                <label>{{ __('Display Name') }}</label>
                {{ $role->display_name }}
            </div>
            <div class="form-group">
                <label>{{ __('Description') }}</label>
                {{ $role->description }}
            </div>
            <div class="form-group">
                <label>{{ __('Permissions') }}</label>
                {{ $role->permissions->count() }} / {{ $totalPermissions }}
            </div>

            @if ($modules->isEmpty())
                <p class="text-muted">{{ __('No permissions') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('Module') }}</th>
                                <th>{{ __('Permissions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($modules as $module)
                                <tr>
                                    <td>{{ $module['name'] }}</td>
                                    <td>
                                        @foreach ($module['permissions'] as $permission)
                                            <span class="badge bg-label-primary me-1">{{ __($permission) }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@stop
