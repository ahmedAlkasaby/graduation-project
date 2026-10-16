<?php

namespace App\Livewire\Pulse;

use App\Pulse\Recorders\HighMemoryRequests as HighMemoryRequestsRecorder;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Url;

/**
 * Dashboard card for the HighMemoryRequests recorder: which endpoints crossed
 * the peak-memory threshold, and how high they went.
 */
#[Lazy]
class HighMemoryRequests extends Card
{
    /**
     * Ordering.
     *
     * @var 'max'|'count'
     */
    #[Url(as: 'high-memory')]
    public string $orderBy = 'max';

    /**
     * Render the component.
     */
    public function render(): Renderable
    {
        [$entries, $time, $runAt] = $this->remember(
            fn () => $this->aggregate(
                'high_memory_request',
                ['max', 'count'],
                match ($this->orderBy) {
                    'count' => 'count',
                    default => 'max',
                },
            )->map(function ($row) {
                [$method, $path] = json_decode($row->key, flags: JSON_THROW_ON_ERROR);

                return (object) [
                    'method' => $method,
                    'path' => $path,
                    'count' => $row->count,
                    'peak' => round($row->max / 1024 / 1024, 1),
                ];
            }),
            $this->orderBy,
        );

        return View::make('livewire.pulse.high-memory-requests', [
            'time' => $time,
            'runAt' => $runAt,
            'entries' => $entries,
            'config' => [
                'threshold' => Config::get('pulse.recorders.'.HighMemoryRequestsRecorder::class.'.threshold', 100),
                'sample_rate' => Config::get('pulse.recorders.'.HighMemoryRequestsRecorder::class.'.sample_rate'),
            ],
        ]);
    }
}
