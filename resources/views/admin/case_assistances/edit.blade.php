@extends('admin.layouts.app', ['head' => 'headForm', 'foot' => 'footForm'])
@section('title') {{ __('Edit Assistance') }}
@stop
@section('head_content')
    @include('admin.layouts.components.head', ['back_url' => route('admin.case_assistances.show', $assistance)])
@stop
@section('content')
    @include('admin.errors.errors')

    <div class="alert alert-info d-flex gap-2 mt-3" role="alert">
        <i class="ti ti-lock"></i>
        <span>{{ __('The beneficiary, type, amount and installment schedule cannot be changed after an assistance is created. Only the title, description, order and status can be edited.') }}</span>
    </div>

    {{-- The fixed financial setup, shown for reference only --}}
    <div class="card mb-4">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">{{ __('Beneficiary') }}</dt>
                <dd class="col-sm-9">{{ $assistance->charityCase?->selectLabel() }}</dd>

                <dt class="col-sm-3">{{ __('Type') }}</dt>
                <dd class="col-sm-9">@include('admin.case_assistances.components.type-badge')</dd>

                <dt class="col-sm-3">{{ __('Amount') }}</dt>
                <dd class="col-sm-9">@include('admin.case_assistances.components.amount')</dd>

                <dt class="col-sm-3">{{ __('Installments') }}</dt>
                <dd class="col-sm-9 mb-0">{{ $assistance->installments_count }}</dd>
            </dl>
        </div>
    </div>

    @include('admin.layouts.forms.edit', ['model' => $assistance, 'table' => 'case_assistances'])
    @include('admin.case_assistances.components.details-fields')
    <div class="mb-4">
        @include('admin.layouts.forms.buttons.form-save')
    </div>
    @include('admin.layouts.forms.close')
@stop
