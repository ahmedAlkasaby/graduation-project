@extends('admin.layouts.app')
@section('title') {{ __('Show User') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => route('admin.users.index')])
@stop
@section('content')
    <div class="card mt-3">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-4">
                @if ($user->image && upload_exists($user->image))
                    <img src="{{ upload_url($user->image) }}" class="rounded" width="80" alt="{{ $user->full_name }}">
                @endif
                <div>
                    <h5 class="mb-1">{{ $user->full_name }}</h5>
                    <span class="badge bg-label-info">{{ userName($user->type) }}</span>
                </div>
            </div>

            <dl class="row mb-0">
                <dt class="col-sm-3">{{ __('Email') }}</dt>
                <dd class="col-sm-9">{{ $user->email }}</dd>

                <dt class="col-sm-3">{{ __('Phone') }}</dt>
                <dd class="col-sm-9">{{ $user->phone }}</dd>

                <dt class="col-sm-3">{{ __('Gender') }}</dt>
                <dd class="col-sm-9">{{ $user->gender ? labelForValue($user->gender) : '' }}</dd>

                <dt class="col-sm-3">{{ __('Birth Date') }}</dt>
                <dd class="col-sm-9">{{ $user->birth_date?->format('Y-m-d') }}</dd>

                <dt class="col-sm-3">{{ __('Language') }}</dt>
                <dd class="col-sm-9">{{ $user->locale }}</dd>

                <dt class="col-sm-3">{{ __('Roles') }}</dt>
                <dd class="col-sm-9">
                    @forelse ($user->roles as $role)
                        <span class="badge bg-label-primary">{{ $role->label() }}</span>
                    @empty
                        -
                    @endforelse
                </dd>

                <dt class="col-sm-3">{{ __('Status') }}</dt>
                <dd class="col-sm-9">{{ statusName((int) $user->active) }}</dd>

                <dt class="col-sm-3">{{ __('Last Active') }}</dt>
                <dd class="col-sm-9">{{ $user->last_active?->diffForHumans() }}</dd>

                <dt class="col-sm-3">{{ __('Created At') }}</dt>
                <dd class="col-sm-9">{{ $user->created_at?->format('Y-m-d H:i') }}</dd>
            </dl>
        </div>
    </div>
@stop
