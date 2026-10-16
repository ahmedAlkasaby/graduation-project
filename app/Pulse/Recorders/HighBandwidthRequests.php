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
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Records requests whose payload (request + response, headers included) is
 * larger than the configured threshold.
 *
 * The response is read from the kernel callback rather than the container:
 * resolving Illuminate\Http\Response would only build an empty response, which
 * would silently reduce the number to request size alone.
 */
class HighBandwidthRequests
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

    public function register(callable $record, Application $app): void
    {
        $this->afterResolving(
            $app,
            Kernel::class,
            fn (Kernel $kernel) => $kernel->whenRequestLifecycleIsLongerThan(-1, $record) // @phpstan-ignore method.notFound
        );
    }

    /**
     * Record the bandwidth used by the request.
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

        $totalBytes = $this->requestSize($request) + $this->responseSize($response);

        if ($totalBytes <= $this->thresholdBytes()) {
            return;
        }

        $this->pulse->record(
            type: 'high_bandwidth_request',
            key: json_encode([$request->method(), $path], flags: JSON_THROW_ON_ERROR),
            value: $totalBytes,
            timestamp: $startedAt,
        )->count()->max()->avg()->sum();
    }

    /**
     * The size of the incoming request, in bytes.
     */
    protected function requestSize(Request $request): int
    {
        // Prefer the declared length: reading php://input for a multipart
        // upload would pull the whole body into memory for nothing.
        $body = (int) $request->headers->get('Content-Length', '0');

        if ($body === 0 && ! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            $body = strlen($request->getContent());
        }

        return $this->headerSize($request->headers->all())
            + strlen($request->fullUrl())
            + $body;
    }

    /**
     * The size of the outgoing response, in bytes.
     */
    protected function responseSize(Response $response): int
    {
        $body = match (true) {
            $response instanceof BinaryFileResponse => (int) $response->getFile()->getSize(),
            $response instanceof StreamedResponse => (int) $response->headers->get('Content-Length', '0'),
            default => strlen((string) $response->getContent()),
        };

        return $this->headerSize($response->headers->all()) + $body;
    }

    /**
     * Approximate the wire size of a header bag.
     *
     * @param  array<string, array<int, string|null>>  $headers
     */
    protected function headerSize(array $headers): int
    {
        $size = 0;

        foreach ($headers as $name => $values) {
            foreach ((array) $values as $value) {
                $size += strlen($name) + strlen((string) $value) + 4; // ": " + CRLF
            }
        }

        return $size;
    }

    /**
     * The configured threshold, in bytes.
     */
    protected function thresholdBytes(): int
    {
        return (int) $this->config->get('pulse.recorders.'.self::class.'.threshold', 1000) * 1024;
    }
}
