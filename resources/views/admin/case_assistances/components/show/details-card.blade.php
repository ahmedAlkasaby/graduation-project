<div class="card h-100">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Assistance') }}</h5>
    </div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-5">{{ __('Description') }}</dt>
            <dd class="col-sm-7">{{ $assistance->descriptionLang($admin_language) ?: '—' }}</dd>

            <dt class="col-sm-5">{{ __('Created By') }}</dt>
            <dd class="col-sm-7">{{ $assistance->creator?->full_name }}</dd>

            <dt class="col-sm-5">{{ __('Created At') }}</dt>
            <dd class="col-sm-7 mb-0">{{ $assistance->created_at?->toDateString() }}</dd>
        </dl>
    </div>
</div>
