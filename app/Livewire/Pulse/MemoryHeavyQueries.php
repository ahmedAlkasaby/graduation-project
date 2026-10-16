<?php

namespace App\Livewire\Pulse;

use App\Pulse\Recorders\MemoryHeavyQueries as MemoryHeavyQueriesRecorder;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Url;

/**
 * Dashboard card for the MemoryHeavyQueries recorder: the queries that pull the
 * most memory into PHP, which the stock "Slow Queries" card cannot show because
 * it only measures time.
 */
#[Lazy]
class MemoryHeavyQueries extends Card
{
    /**
     * Ordering.
     *
     * @var 'heaviest'|'count'
     */
    #[Url(as: 'memory-heavy-queries')]
    public string $orderBy = 'heaviest';

    /**
     * Render the component.
     */
    public function render(): Renderable
    {
        [$queries, $time, $runAt] = $this->remember(
            fn () => $this->aggregate(
                'memory_heavy_query',
                ['max', 'count'],
                match ($this->orderBy) {
                    'count' => 'count',
                    default => 'max',
                },
            )->map(function ($row) {
                [$sql, $location] = json_decode($row->key, flags: JSON_THROW_ON_ERROR);

                return (object) [
                    'sql' => $sql,
                    'location' => $location,
                    'count' => $row->count,
                    'heaviest' => round($row->max / 1024 / 1024, 1),
                ];
            }),
            $this->orderBy,
        );

        return View::make('livewire.pulse.memory-heavy-queries', [
            'time' => $time,
            'runAt' => $runAt,
            'queries' => $queries,
            'config' => [
                'threshold' => Config::get('pulse.recorders.'.MemoryHeavyQueriesRecorder::class.'.threshold', 10),
                'sample_rate' => Config::get('pulse.recorders.'.MemoryHeavyQueriesRecorder::class.'.sample_rate'),
            ],
        ]);
    }
}
