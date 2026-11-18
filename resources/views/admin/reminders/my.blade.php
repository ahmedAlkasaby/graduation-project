@extends('admin.layouts.app')

@section('title')
    {{ __('Reminders') }}
@stop

@section('after_head')
    <style>
        .reminders-calendar { width: 100%; table-layout: fixed; }
        .reminders-calendar th { font-size: .75rem; font-weight: 500; color: #a7acb2; text-align: center; padding: .35rem 0; }
        .reminders-calendar td { text-align: center; padding: .2rem 0; }
        .reminders-day {
            display: inline-flex; align-items: center; justify-content: center;
            width: 2rem; height: 2rem; border-radius: 50%;
            font-size: .8125rem; color: #566a7f; text-decoration: none;
        }
        .reminders-day:hover { background: #f1f3f7; color: #566a7f; }
        .reminders-day.is-muted { color: #c7cbd1; }
        .reminders-day.has-reminders { background: #4db5ff; color: #fff; }
        .reminders-day.is-today { background: #1b6ff0; color: #fff; font-weight: 600; }
        .reminders-day.is-selected { box-shadow: 0 0 0 2px #1b6ff0; }
        .reminder-card { background: #f8f9fb; border-radius: .5rem; padding: 1rem 1.25rem; }
        .reminder-card + .reminder-card { margin-top: .75rem; }
        .reminder-badge { font-size: .6875rem; font-weight: 600; padding: .25rem .5rem; border-radius: .25rem; }
        .reminder-meta { font-size: .75rem; color: #a7acb2; }
        .reminder-status { font-size: .6875rem; font-weight: 600; }
    </style>
@stop

@section('content')
    @include('admin.errors.alerts')

    <div class="d-flex flex-wrap justify-content-between align-items-end mb-3">
        <div>
            <div class="text-muted small">{{ __('Stay on top of upcoming events and important dates') }}</div>
            <h4 class="fw-bold mb-0">{{ __('Reminders') }}</h4>
        </div>
        @if ($route_create)
            <a href="{{ route('admin.reminders.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i>{{ __('Add Reminder') }}
            </a>
        @endif
    </div>

    {{-- The screen's three controls; the calendar keeps the month it is on while they change. --}}
    <form method="GET" action="{{ route('admin.my_reminders.index') }}" class="row g-2 mb-4">
        <input type="hidden" name="month" value="{{ $month->format('Y-m') }}">
        @if (! empty($filters['date']))
            <input type="hidden" name="date" value="{{ $filters['date'] }}">
        @endif
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text"><i class="ti ti-search"></i></span>
                <input type="text" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}"
                       placeholder="{{ __('Search reminders') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">{{ __('All Categories') }}</option>
                @foreach ($categoryOptions as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['category'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">{{ __('All') }}</option>
                @foreach ($statusOptions as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-outline-primary">{{ __('Filter') }}</button>
        </div>
    </form>

    <div class="row g-4">
        {{-- Calendar and Categories --}}
        <div class="col-lg-4">
            @php
                $today = \Illuminate\Support\Carbon::today();
                $gridStart = $month->copy()->startOfMonth()->startOfWeek(\Carbon\CarbonInterface::SUNDAY);
                $gridEnd = $month->copy()->endOfMonth()->endOfWeek(\Carbon\CarbonInterface::SATURDAY);
            @endphp

            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <a class="btn btn-sm btn-text-secondary"
                           href="{{ route('admin.my_reminders.index', array_merge($filters, ['month' => $month->copy()->subMonth()->format('Y-m'), 'date' => null])) }}">
                            <i class="ti ti-chevron-left"></i>
                        </a>
                        <h6 class="fw-semibold mb-0">{{ $month->translatedFormat('F Y') }}</h6>
                        <a class="btn btn-sm btn-text-secondary"
                           href="{{ route('admin.my_reminders.index', array_merge($filters, ['month' => $month->copy()->addMonth()->format('Y-m'), 'date' => null])) }}">
                            <i class="ti ti-chevron-right"></i>
                        </a>
                    </div>

                    <table class="reminders-calendar">
                        <thead>
                            <tr>
                                @for ($weekday = $gridStart->copy(); $weekday->lt($gridStart->copy()->addDays(7)); $weekday->addDay())
                                    <th>{{ mb_substr($weekday->translatedFormat('D'), 0, 1) }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @for ($day = $gridStart->copy(); $day->lte($gridEnd); $day->addDay())
                                @if ($day->dayOfWeek === \Carbon\CarbonInterface::SUNDAY)
                                    <tr>
                                @endif
                                @php
                                    $key = $day->toDateString();
                                    $count = (int) ($calendar[$key] ?? 0);
                                    $classes = collect(['reminders-day'])
                                        ->when($day->month !== $month->month, fn ($c) => $c->push('is-muted'))
                                        ->when($count > 0, fn ($c) => $c->push('has-reminders'))
                                        ->when($day->isSameDay($today), fn ($c) => $c->push('is-today'))
                                        ->when(($filters['date'] ?? null) === $key, fn ($c) => $c->push('is-selected'))
                                        ->implode(' ');
                                @endphp
                                <td>
                                    <a class="{{ $classes }}"
                                       title="{{ $count ? trans_choice(':count reminder|:count reminders', $count, ['count' => $count]) : '' }}"
                                       href="{{ route('admin.my_reminders.index', array_merge($filters, ['month' => $month->format('Y-m'), 'date' => $key])) }}">
                                        {{ $day->day }}
                                    </a>
                                </td>
                                @if ($day->dayOfWeek === \Carbon\CarbonInterface::SATURDAY)
                                    </tr>
                                @endif
                            @endfor
                        </tbody>
                    </table>

                    @if (! empty($filters['date']))
                        <div class="text-center mt-2">
                            <a class="small"
                               href="{{ route('admin.my_reminders.index', array_merge($filters, ['month' => $month->format('Y-m'), 'date' => null])) }}">
                                {{ __('Clear') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">{{ __('Categories') }}</h6>
                    @foreach ($categories['counts'] as $category)
                        <div class="d-flex justify-content-between align-items-center py-1">
                            <span class="reminder-meta" style="color: {{ $category['color'] }}">{{ $category['label'] }}</span>
                            <span class="fw-semibold small">
                                {{ trans_choice(':count reminder|:count reminders', $category['count'], ['count' => $category['count']]) }}
                            </span>
                        </div>
                    @endforeach
                    <hr class="my-3">
                    <div class="reminder-meta">
                        @if ($categories['next_at'])
                            {{ __('Your next reminder is on :date', ['date' => \Illuminate\Support\Carbon::parse($categories['next_at'])->translatedFormat('j F \a\t h:i A')]) }}
                        @else
                            {{ __('You have no upcoming reminders') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Upcoming Reminders --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">{{ __('Upcoming Reminders') }}</h6>

                    @forelse ($data as $recipient)
                        @php
                            $reminder = $recipient->reminder;
                            $category = \App\Enums\ReminderCategoryEnum::tryFrom((string) $reminder?->category);
                            $repeat = $reminder?->repeat_rule ?? \App\Enums\ReminderRepeatEnum::None;
                        @endphp
                        <div class="reminder-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <span class="reminder-badge"
                                      style="color: {{ $category?->color() ?? '#697a8d' }}; background: {{ $category?->color() ?? '#697a8d' }}1a;">
                                    {{ $category?->label() ?? $reminder?->category }} &middot; {{ $repeat->label() }}
                                </span>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-text-secondary p-0" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        @foreach (\App\Enums\ReminderRecipientStatusEnum::settable() as $status)
                                            @continue($status === $recipient->status)
                                            <form method="POST" action="{{ route('admin.my_reminders.status', $recipient->id) }}">
                                                @csrf
                                                <input type="hidden" name="status" value="{{ $status->value }}">
                                                <button type="submit" class="dropdown-item">{{ __('Mark as :status', ['status' => $status->label()]) }}</button>
                                            </form>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="fw-semibold mt-2">{{ $reminder?->title }}</div>
                            @if ($reminder?->description)
                                <div class="reminder-meta mt-1">{{ $reminder->description }}</div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="reminder-meta">{{ $recipient->remind_at?->translatedFormat('d M Y · h:i A') }}</span>
                                <span class="reminder-status" style="color: {{ $recipient->status?->color() }}">
                                    {{ $recipient->status?->label() }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">{{ __('No reminders found') }}</div>
                    @endforelse

                    <div class="mt-3">
                        {{ $data->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
