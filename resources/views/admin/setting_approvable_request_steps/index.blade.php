@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title')
    {{ __('Approval Flows') }}
@stop
@section('content')
    @include('admin.errors.alerts')
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-1">{{ __('Approval Flows') }}</h5>
            <p class="text-muted mb-0">
                {{ __('Each request type passes through its reviewers in order. The last step makes the final decision.') }}
            </p>
        </div>
    </div>

    <div class="row g-4">
        @foreach ($flows as $flow)
            @include('admin.setting_approvable_request_steps.components.flow-card', ['flow' => $flow])
        @endforeach
    </div>

    @if ($route_edit)
        @foreach ($flows as $flow)
            @include('admin.setting_approvable_request_steps.components.flow-modal', ['flow' => $flow])
        @endforeach

        <template id="approval-flow-step-template">
            @include('admin.setting_approvable_request_steps.components.step-row', ['selectedRole' => null])
        </template>
    @endif
@stop
@section('after_foot')
    @if ($route_edit)
        @include('admin.setting_approvable_request_steps.components.form-js')
    @endif
@stop
