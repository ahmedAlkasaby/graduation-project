<?php

namespace App\Livewire\Pulse;

use App\Pulse\Recorders\EndpointMetrics;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Url;

/**
 * Dashboard card for the EndpointMetrics recorder: traffic and average cost
 * per endpoint, which the stock "Slow Requests" card cannot show because it
 * only stores threshold-crossing requests.
 */
#[Lazy]
class Endpoints extends Card
{
    /**
     * Ordering.
     *
     * @var 'count'|'avg'|'max'
     */
    #[Url(as: 'endpoints')]
    public string $orderBy = 'count';

    /**
     * Render the component.
     */
    public function render(): Renderable
    {
        [$endpoints, $time, $runAt] = $this->remember(
            fn () => $this->aggregate(
                'endpoint_request',
                ['count', 'avg', 'max'],
                match ($this->orderBy) {
                    'avg' => 'avg',
                    'max' => 'max',
                    default => 'count',
                },
            )->map(function ($row) {
                [$method, $uri] = json_decode($row->key, flags: JSON_THROW_ON_ERROR);

                return (object) [
                    'uri' => $uri,
                    'method' => $method,
                    'count' => $row->count,
                    'average' => $row->avg,
                    'slowest' => $row->max,
                ];
            }),
            $this->orderBy,
        );

        return View::make('livewire.pulse.endpoints', [
            'time' => $time,
            'runAt' => $runAt,
            'endpoints' => $endpoints,
            'config' => [
                'sample_rate' => Config::get('pulse.recorders.'.EndpointMetrics::class.'.sample_rate'),
            ],
        ]);
    }
}
