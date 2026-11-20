{{ number_format((float) $assistance->amount(), 2) }} {{ $currency }}@if ($assistance->isRecurring()) <small class="text-muted">/ {{ __('month') }}</small>@endif
