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
                {{ $role->description  }}
            </div>
            @if($role_edit > 0)
            <div class="form-group">
                <label>{{ __('Permissions') }}</label>
                @if(!empty($role->perms))
                @foreach($role->perms as $v)
                <label class="label label-success">{{ $v->display_name }}</label>
                @endforeach
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@stop