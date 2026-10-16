<?php

namespace App\Livewire\Pulse;

use App\Pulse\Recorders\HighBandwidthRequests as HighBandwidthRequestsRecorder;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Url;

/**
 * Dashboard card for the HighBandwidthRequests recorder: which endpoints move
 * the most bytes, by largest single transfer, running total, or hit count.
 */
#[Lazy]
class HighBandwidthRequests extends Card
{
    /**
     * Ordering.
     *
     * @var 'largest'|'total'|'count'
     */
    #[Url(as: 'high-bandwidth')]
    public string $orderBy = 'largest';

    /**
     * Render the component.
     */
    public function render(): Renderable
    {
        [$entries, $time, $runAt] = $this->remember(
            fn () => $this->aggregate(
                'high_bandwidth_request',
                ['max', 'avg', 'sum', 'count'],
                match ($this->orderBy) {
                    'count' => 'count',
                    'total' => 'sum',
                    default => 'max',
                },
            )->map(function ($row) {
                [$method, $path] = json_decode($row->key, flags: JSON_THROW_ON_ERROR);

                return (object) [
                    'method' => $method,
                    'path' => $path,
                    'count' => $row->count,
                    'largest' => round($row->max / 1024 / 1024, 2),
                    'average' => round($row->avg / 1024 / 1024, 2),
                    'total' => round($row->sum / 1024 / 1024, 2),
                ];
            }),
            $this->orderBy,
        );

        return View::make('livewire.pulse.high-bandwidth-requests', [
            'time' => $time,
            'runAt' => $runAt,
            'entries' => $entries,
            'config' => [
                'threshold' => Config::get('pulse.recorders.'.HighBandwidthRequestsRecorder::class.'.threshold', 1000),
                'sample_rate' => Config::get('pulse.recorders.'.HighBandwidthRequestsRecorder::class.'.sample_rate'),
            ],
        ]);
    }
}
