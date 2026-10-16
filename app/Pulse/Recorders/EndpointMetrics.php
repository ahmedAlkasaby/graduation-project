<?php

namespace App\Pulse\Recorders;

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
 * Records every matched request, not just the slow ones.
 *
 * Pulse ships SlowRequests, which only fires above a threshold and only keeps a
 * count and a max — enough to answer "what is slowest", not "how much traffic
 * does this endpoint get" or "what does it normally cost". This recorder keeps
 * count/avg/max per endpoint so both questions are answerable.
 *
 * The key is the route's URI pattern ("api/cities/{city}"), never the literal
 * path, so requests collapse onto their endpoint instead of creating a row per
 * id. Requests that match no route are skipped for the same reason: unrouted
 * bot traffic would grow the key space without bound.
 */
class EndpointMetrics
{
    use Concerns\Ignores,
        Concerns\LivewireRoutes,
        Concerns\Sampling,
        ConfiguresAfterResolving;

    /**
     * Create a new recorder instance.
     */
    public function __construct(
        protected Pulse $pulse,
    ) {
        //
    }

    /**
     * Register the recorder.
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
     * Record the request.
     */
    public function record(Carbon $startedAt, Request $request, Response $response): void
    {
        if (! $request->route() instanceof Route || ! $this->shouldSample()) {
            return;
        }

        [$path, $via] = $this->resolveRoutePath($request);

        if ($this->shouldIgnore($path)) {
            return;
        }

        $duration = (int) $startedAt->diffInMilliseconds();

        $this->pulse->record(
            type: 'endpoint_request',
            key: json_encode([$request->method(), $path], flags: JSON_THROW_ON_ERROR),
            value: $duration,
            timestamp: $startedAt,
        )->count()->avg()->max();
    }
}
