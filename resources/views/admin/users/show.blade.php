@extends('admin.layouts.app')
@section('title') {{ __('Show User') }}
@stop
@section('head_content')
@include('admin.layouts.components.head', ['back_url' => $previous_url ?? route('admin.users.index')])
@stop
@section('content')

<div class="row">
    <div class="box">
        <div class="box-body">


            <div class="form-group">

                <label>{{ __('Login name') }}</label>

                {{ $user->username }}

            </div>

            <div class="form-group">

                <label>{{ __('Name') }}</label>

                {{ $user->name }}

            </div>


            <div class="form-group">

                <label>{{ __('Email') }}</label>

                {{ $user->email }}

            </div>

            <div class="form-group">

                <label>{{ __('Phone') }}</label>

                {{ $user->phone }}

            </div>

            <div class="form-group">

                <label>{{ __('Address') }}</label>

                {{ $user->address }}

            </div>

            <div class="form-group">

                <label>{{ __('Image') }}</label>

                <img  src="{{ $user->image }}"  width="20%" height="auto" @if($user->image == Null)  style="display:none;" @endif />


            </div>
            @if($user_active > 0)


            <div class="form-group">
                <label>{{ __('Roles') }}</label>
                @if(!empty($user->roles))
                @foreach($user->roles as $v)
                <label class="label label-success">{{ $v->display_name }}</label>
                @endforeach
                @endif
            </div>
            <div class="form-group">
                <label>{{ __('Status') }}</label>
                {{ statusName($user->is_active) }}
            </div>
            @endif
        </div>
    </div>
</div>

@stop