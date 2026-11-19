@extends('admin.layouts.app')
@section('title') {{ __('Show Role') }}
@stop
@section('head_content')
@include('admin.layouts.components.head', ['back_url' => route('admin.roles.index')])
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
            @foreach (['can_login_web' => __('Web dashboard'), 'can_login_mobile' => __('Mobile app')] as $platformField => $platformLabel)
                <div class="form-group">
                    <label>{{ $platformLabel }}</label>
                    @if ($role->{$platformField})
                        <span class="badge bg-label-success">{{ __('Allowed') }}</span>
                    @else
                        <span class="badge bg-label-secondary">{{ __('Not allowed') }}</span>
                    @endif
                </div>
            @endforeach
            <div class="form-group">
                <label>{{ __('Permissions') }}</label>
                {{ $role->permissions->count() }} / {{ $totalPermissions }}
            </div>
            <div class="form-group">
                <label>{{ __('Users') }}</label>
                {{ $role->users_count }}
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
                                            <span class="badge bg-label-primary me-1">{{ $permission }}</span>
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
