<?php

declare(strict_types=1);

namespace App\Pulse\Recorders;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Events\ConnectionEstablished;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Str;
use Laravel\Pulse\Concerns\ConfiguresAfterResolving;
use Laravel\Pulse\Pulse;
use Laravel\Pulse\Recorders\Concerns;

/**
 * Records queries that allocate more memory than the configured threshold.
 *
 * Pulse ships SlowQueries (time based) but has nothing for the memory a single
 * query pulls into PHP, which is what an unbounded select of every column
 * actually costs. Memory is sampled with beforeExecuting on every connection
 * and diffed when QueryExecuted fires: the raw result set has been fetched by
 * then, but models have not been hydrated yet.
 */
class MemoryHeavyQueries
{
    use Concerns\Ignores,
        Concerns\Sampling,
        ConfiguresAfterResolving;

    /**
     * The events to listen for.
     *
     * @var class-string
     */
    public string $listen = QueryExecuted::class;

    /**
     * Memory usage captured immediately before the current query ran.
     */
    protected int $memoryBefore = 0;

    public function __construct(
        protected Pulse $pulse,
        protected Repository $config,
    ) {
        //
    }

    /**
     * Attach the memory snapshot to every database connection.
     */
    public function register(callable $record, Application $app): void
    {
        $snapshot = fn () => $this->memoryBefore = memory_get_usage();

        $this->afterResolving($app, 'events', fn (Dispatcher $events) => $events->listen(
            ConnectionEstablished::class,
            fn (ConnectionEstablished $event) => $event->connection->beforeExecuting($snapshot),
        ));

        // Connections resolved before Pulse booted do not fire the event above.
        $this->afterResolving($app, 'db', function (DatabaseManager $db) use ($snapshot) {
            foreach ($db->getConnections() as $connection) {
                $connection->beforeExecuting($snapshot);
            }
        });
    }

    /**
     * Record a memory heavy query.
     */
    public function record(QueryExecuted $event): void
    {
        [$consumed, $timestampMs, $duration, $sql, $location] = [
            memory_get_usage() - $this->memoryBefore,
            CarbonImmutable::now()->getTimestampMs(),
            (int) $event->time,
            $event->sql,
            $this->config->get('pulse.recorders.'.self::class.'.location')
                ? $this->resolveLocation()
                : null,
        ];

        $this->pulse->lazy(function () use ($consumed, $timestampMs, $duration, $sql, $location) {
            if (
                ! $this->shouldSample() ||
                $this->shouldIgnore($sql) ||
                $consumed < $this->thresholdBytes()
            ) {
                return;
            }

            if ($maxQueryLength = $this->config->get('pulse.recorders.'.self::class.'.max_query_length')) {
                $sql = Str::limit($sql, $maxQueryLength);
            }

            $this->pulse->record(
                type: 'memory_heavy_query',
                key: json_encode([$sql, $location], flags: JSON_THROW_ON_ERROR),
                value: $consumed,
                timestamp: (int) (($timestampMs - $duration) / 1000),
            )->max()->count();
        });
    }

    /**
     * The configured threshold, in bytes.
     */
    protected function thresholdBytes(): int
    {
        return (int) $this->config->get('pulse.recorders.'.self::class.'.threshold', 10) * 1024 * 1024;
    }

    /**
     * Resolve the application frame the query was issued from.
     */
    protected function resolveLocation(): string
    {
        $frame = collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS))
            ->skip(2)
            ->firstWhere(fn (array $frame) => isset($frame['file']) && ! $this->isInternalFile($frame['file']));

        if ($frame === null) {
            return '';
        }

        return $this->formatLocation($frame['file'] ?? 'unknown', $frame['line'] ?? null);
    }

    /**
     * Determine whether a file should be considered internal.
     */
    protected function isInternalFile(string $file): bool
    {
        return Str::startsWith($file, base_path('vendor'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR.'pulse'))
            || Str::startsWith($file, base_path('vendor'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR.'framework'))
            || $file === base_path('artisan')
            || $file === public_path('index.php');
    }

    /**
     * Format a file and line number and strip the base path.
     */
    protected function formatLocation(string $file, ?int $line): string
    {
        return Str::replaceFirst(base_path(DIRECTORY_SEPARATOR), '', $file).(is_int($line) ? (':'.$line) : '');
    }
}
