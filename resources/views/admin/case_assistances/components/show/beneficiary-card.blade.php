<div class="card h-100">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ __('Beneficiary') }}</h5>
        @if ($canShowCase)
            <a href="{{ route('admin.charity_cases.show', $assistance->charityCase) }}" class="btn btn-sm btn-label-primary">{{ __('Open Profile') }}</a>
        @endif
    </div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-5">{{ __('Full Name') }}</dt>
            <dd class="col-sm-7">{{ $assistance->charityCase?->full_name }}</dd>

            <dt class="col-sm-5">{{ __('National ID') }}</dt>
            <dd class="col-sm-7">{{ $assistance->charityCase?->national_id }}</dd>

            <dt class="col-sm-5">{{ __('Phone') }}</dt>
            <dd class="col-sm-7">{{ $assistance->charityCase?->phone }}</dd>

            <dt class="col-sm-5">{{ __('Category') }}</dt>
            <dd class="col-sm-7">{{ $assistance->category?->nameLang($admin_language) }}</dd>

            <dt class="col-sm-5">{{ __('Volunteer') }}</dt>
            <dd class="col-sm-7 mb-0">{{ $assistance->charityCase?->volunteer?->full_name }}</dd>
        </dl>
    </div>
</div>
