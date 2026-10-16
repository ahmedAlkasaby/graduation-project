<?php

declare(strict_types=1);

namespace App\Pulse\Recorders;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Carbon;
use Laravel\Pulse\Concerns\ConfiguresAfterResolving;
use Laravel\Pulse\Pulse;
use Laravel\Pulse\Recorders\Concerns;
use Symfony\Component\HttpFoundation\Response;

/**
 * Records HTTP requests whose peak memory crosses the configured threshold.
 *
 * Pulse ships SlowRequests (time based) but nothing for memory, which is what
 * an unpaginated listing or an in-memory export actually costs. Only the
 * crossing requests are stored, so a healthy request writes no rows.
 */
class HighMemoryRequests
{
    use Concerns\Ignores,
        Concerns\LivewireRoutes,
        Concerns\Sampling,
        ConfiguresAfterResolving;

    public function __construct(
        protected Pulse $pulse,
        protected Repository $config,
    ) {
        //
    }

    /**
     * Register the recorder.
     *
     * The -1 threshold makes the kernel hand every request to the callback;
     * the memory check below decides what is worth recording.
     */
    public function register(callable $record, Application $app): void
    {
        $this->afterResolving(
            $app,
            Kernel::class,
            fn (Kernel $kernel) => $kernel->whenRequestLifecycleIsLongerThan(-1, $record) // @phpstan-ignore method.notFound
        );
    }

    /**
     * Record the peak memory used by the request.
     */
    public function record(Carbon $startedAt, Request $request, Response $response): void
    {
        if (! $request->route() instanceof Route || ! $this->shouldSample()) {
            return;
        }

        [$path] = $this->resolveRoutePath($request);

        if ($this->shouldIgnore($path)) {
            return;
        }

        $peakBytes = memory_get_peak_usage(true);

        if ($peakBytes <= $this->thresholdBytes()) {
            return;
        }

        $this->pulse->record(
            type: 'high_memory_request',
            key: json_encode([$request->method(), $path], flags: JSON_THROW_ON_ERROR),
            value: $peakBytes,
            timestamp: $startedAt,
        )->max()->count();
    }

    /**
     * The configured threshold, in bytes.
     */
    protected function thresholdBytes(): int
    {
        return (int) $this->config->get('pulse.recorders.'.self::class.'.threshold', 100) * 1024 * 1024;
    }
}
