@extends('admin.layouts.app')
@section('title') {{ __('Permissions') }}
@stop
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-1">{{ __('Permissions') }}</h5>
            <small class="text-muted">{{ __('What a role can be given. Assign them from the role form.') }}</small>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead class="border-top">
                    <tr>
                        <th>{{ __('Module') }}</th>
                        <th>{{ __('Permissions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($resources as $resource)
                        <tr>
                            <td>
                                <span class="fw-medium">{{ $resource['module_name'] }}</span>
                                <small class="d-block text-muted">{{ $resource['module'] }}</small>
                            </td>
                            <td class="text-wrap">
                                @foreach ($resource['permissions'] as $permission)
                                    <span class="badge bg-label-primary me-1 mb-1" title="{{ $permission['name'] }}">{{ $permission['label'] }}</span>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                    @if ($custom->isNotEmpty())
                        <tr>
                            <td><span class="fw-medium">{{ __('Custom Permissions') }}</span></td>
                            <td class="text-wrap">
                                @foreach ($custom as $permission)
                                    <span class="badge bg-label-warning me-1 mb-1" title="{{ $permission['name'] }}">{{ $permission['label'] }}</span>
                                @endforeach
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@stop
