{{--
    Run payroll for every active employee for a chosen month. Recalculating a
    month that already ran just updates it; a finalized (paid) run is skipped.
--}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Generate payroll') }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.payrolls.generate') }}" method="POST" class="row g-3 align-items-end"
            onsubmit="return confirm('{{ __('Generate payroll for all active employees for the selected month?') }}');">
            @csrf
            <div class="col-sm-4">
                <label class="form-label">{{ __('Month') }}</label>
                <select name="month" class="form-select" required>
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}" @selected((int) request('month', now()->month) === $m)>
                            {{ \Illuminate\Support\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4">
                <label class="form-label">{{ __('Year') }}</label>
                <select name="year" class="form-select" required>
                    @foreach (range(now()->year, now()->year - 5) as $y)
                        <option value="{{ $y }}" @selected((int) request('year', now()->year) === $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-player-play me-1"></i>{{ __('Generate') }}
                </button>
            </div>
        </form>
    </div>
</div>
