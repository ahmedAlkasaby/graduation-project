<?php

use App\Pulse\Recorders\EndpointMetrics;
use App\Pulse\Recorders\HighBandwidthRequests;
use App\Pulse\Recorders\HighMemoryRequests;
use App\Pulse\Recorders\MemoryHeavyQueries;
use Laravel\Pulse\Http\Middleware\Authorize;
use Laravel\Pulse\Pulse;
use Laravel\Pulse\Recorders;

return [

    /*
    |--------------------------------------------------------------------------
    | Pulse Domain
    |--------------------------------------------------------------------------
    |
    | This is the subdomain which the Pulse dashboard will be accessible from.
    | When set to null, the dashboard will reside under the same domain as
    | the application. Remember to configure your DNS entries correctly.
    |
    */

    'domain' => env('PULSE_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Pulse Path
    |--------------------------------------------------------------------------
    |
    | This is the path which the Pulse dashboard will be accessible from. Feel
    | free to change this path to anything you'd like. Note that this won't
    | affect the path of the internal API that is never exposed to users.
    |
    */

    'path' => env('PULSE_PATH', 'pulse'),

    /*
    |--------------------------------------------------------------------------
    | Pulse Master Switch
    |--------------------------------------------------------------------------
    |
    | This configuration option may be used to completely disable all Pulse
    | data recorders regardless of their individual configurations. This
    | provides a single option to quickly disable all Pulse recording.
    |
    */

    'enabled' => env('PULSE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Pulse Storage Driver
    |--------------------------------------------------------------------------
    |
    | This configuration option determines which storage driver will be used
    | while storing entries from Pulse's recorders. In addition, you also
    | may provide any options to configure the selected storage driver.
    |
    */

    'storage' => [
        'driver' => env('PULSE_STORAGE_DRIVER', 'database'),

        'trim' => [
            'keep' => env('PULSE_STORAGE_KEEP', '7 days'),
        ],

        'database' => [
            'connection' => env('PULSE_DB_CONNECTION'),
            'chunk' => 1000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pulse Ingest Driver
    |--------------------------------------------------------------------------
    |
    | This configuration options determines the ingest driver that will be used
    | to capture entries from Pulse's recorders. Ingest drivers are great to
    | free up your request workers quickly by offloading the data storage.
    |
    */

    'ingest' => [
        'driver' => env('PULSE_INGEST_DRIVER', 'storage'),

        'buffer' => env('PULSE_INGEST_BUFFER', 5_000),

        'trim' => [
            'lottery' => [1, 1_000],
            'keep' => env('PULSE_INGEST_KEEP', '7 days'),
        ],

        'redis' => [
            'connection' => env('PULSE_REDIS_CONNECTION'),
            'chunk' => 1000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pulse Cache Driver
    |--------------------------------------------------------------------------
    |
    | This configuration option determines the cache driver that will be used
    | for various tasks, including caching dashboard results, establishing
    | locks for events that should only occur on one server and signals.
    |
    */

    'cache' => env('PULSE_CACHE_DRIVER'),

    /*
    |--------------------------------------------------------------------------
    | Pulse Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will be assigned to every Pulse route, giving you the
    | chance to add your own middleware to this list or change any of the
    | existing middleware. Of course, reasonable defaults are provided.
    |
    */

    'middleware' => [
        'web',
        'admin',          // App\Http\Middleware\AdminPanel — sends guests to admin.login.
        Authorize::class, // Checks the "viewPulse" gate in AuthServiceProvider.
    ],

    /*
    |--------------------------------------------------------------------------
    | Pulse Recorders
    |--------------------------------------------------------------------------
    |
    | The following array lists the "recorders" that will be registered with
    | Pulse, along with their configuration. Recorders gather application
    | event data from requests and tasks to pass to your ingest driver.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Recorder tuning (benchmark-driven)
    |--------------------------------------------------------------------------
    |
    | Measured on GET /admin/login, 250 requests per run, interleaved with a
    | PULSE_ENABLED=false control to cancel machine drift. Overhead is median
    | delta vs the adjacent control run:
    |
    |   CacheInteractions  +18.5ms, 37 rows/request  -> the cost was one hot key
    |                                                   read 37x per request.
    |                                                   AppSettingsService now
    |                                                   memoizes per request, so
    |                                                   this recorder is cheap
    |                                                   and nothing is ignored.
    |   EndpointMetrics    +11.5ms,  1 row/request   -> kept; it is the only
    |                                                   source of per-endpoint
    |                                                   count + average
    |   Exceptions          +8.6ms,  1 row/request   -> that row-per-request came
    |                                                   from a package failing on
    |                                                   a missing table; it now
    |                                                   writes 0 rows on a
    |                                                   healthy request
    |   UserRequests        +2.8ms (noise), 0 rows   -> kept, effectively free
    |   SlowRequests       within noise at every threshold from 100-2000ms
    |
    | Queue-family recorders default to false: this app runs QUEUE_CONNECTION=
    | sync with no app/Jobs, so they can never record anything. Set the matching
    | PULSE_*_ENABLED env var to true if queued work is introduced.
    |
    | Every value below is env-driven; the literals are fallbacks that match the
    | benchmarked configuration, so a missing .env cannot silently regress it.
    |
    */

    'recorders' => [
        /*
        | Every matched request, keyed by route pattern, with count/avg/max.
        | Answers "how much traffic" and "how slow on average" per endpoint;
        | SlowRequests below only answers "what crossed the threshold".
        */
        EndpointMetrics::class => [
            'enabled' => env('PULSE_ENDPOINT_METRICS_ENABLED', true),
            'sample_rate' => env('PULSE_ENDPOINT_METRICS_SAMPLE_RATE', 1),
            'ignore' => [
                '#^/'.env('PULSE_PATH', 'pulse').'#', // Pulse dashboard and its polling...
                '#^/livewire#', // Livewire round-trips...
                '#^/up$#', // Health check...
            ],
        ],

        /*
        | Requests whose peak memory crossed the threshold. SlowRequests below
        | only measures time, so an unpaginated listing or an in-memory export
        | stays invisible there until it is slow as well as fat.
        */
        HighMemoryRequests::class => [
            'enabled' => env('PULSE_HIGH_MEMORY_REQUESTS_ENABLED', true),
            'sample_rate' => env('PULSE_HIGH_MEMORY_REQUESTS_SAMPLE_RATE', 1),
            'threshold' => env('PULSE_HIGH_MEMORY_REQUESTS_THRESHOLD', 100), // MB
            'ignore' => [
                '#^/'.env('PULSE_PATH', 'pulse').'#', // Pulse dashboard and its polling...
                '#^/livewire#', // Livewire round-trips...
                '#^/up$#', // Health check...
            ],
        ],

        /*
        | Requests that moved more than the threshold over the wire, request and
        | response together. Catches the endpoint returning a whole table as
        | JSON long before anyone notices the mobile bill.
        */
        HighBandwidthRequests::class => [
            'enabled' => env('PULSE_HIGH_BANDWIDTH_REQUESTS_ENABLED', true),
            'sample_rate' => env('PULSE_HIGH_BANDWIDTH_REQUESTS_SAMPLE_RATE', 1),
            'threshold' => env('PULSE_HIGH_BANDWIDTH_REQUESTS_THRESHOLD', 1000), // KB
            'ignore' => [
                '#^/'.env('PULSE_PATH', 'pulse').'#', // Pulse dashboard and its polling...
                '#^/livewire#', // Livewire round-trips...
                '#^/up$#', // Health check...
            ],
        ],

        /*
        | Queries that allocated more than the threshold in PHP. SlowQueries
        | below is time based; a fast query returning 50k rows costs memory,
        | not milliseconds, and only shows up here.
        */
        MemoryHeavyQueries::class => [
            'enabled' => env('PULSE_MEMORY_HEAVY_QUERIES_ENABLED', true),
            'sample_rate' => env('PULSE_MEMORY_HEAVY_QUERIES_SAMPLE_RATE', 1),
            'threshold' => env('PULSE_MEMORY_HEAVY_QUERIES_THRESHOLD', 10), // MB
            'location' => env('PULSE_MEMORY_HEAVY_QUERIES_LOCATION', true),
            'max_query_length' => env('PULSE_MEMORY_HEAVY_QUERIES_MAX_QUERY_LENGTH', 1000),
            'ignore' => [
                '/(["`])pulse_[\w]+?\1/', // Pulse tables...
            ],
        ],

        Recorders\CacheInteractions::class => [
            'enabled' => env('PULSE_CACHE_INTERACTIONS_ENABLED', true),
            'sample_rate' => env('PULSE_CACHE_INTERACTIONS_SAMPLE_RATE', 1),
            'ignore' => [
                ...Pulse::defaultVendorCacheKeys(),
            ],
            'groups' => [
                '/^job-exceptions:.*/' => 'job-exceptions:*',
                // '/:\d+/' => ':*',
            ],
        ],

        Recorders\Exceptions::class => [
            'enabled' => env('PULSE_EXCEPTIONS_ENABLED', true),
            'sample_rate' => env('PULSE_EXCEPTIONS_SAMPLE_RATE', 1),
            'location' => env('PULSE_EXCEPTIONS_LOCATION', true),
            'ignore' => [
                // '/^Package\\\\Exceptions\\\\/',
            ],
        ],

        Recorders\Queues::class => [
            'enabled' => env('PULSE_QUEUES_ENABLED', false),
            'sample_rate' => env('PULSE_QUEUES_SAMPLE_RATE', 1),
            'ignore' => [
                // '/^Package\\\\Jobs\\\\/',
            ],
        ],

        Recorders\Servers::class => [
            'server_name' => env('PULSE_SERVER_NAME', gethostname()),
            'directories' => explode(':', env('PULSE_SERVER_DIRECTORIES', '/')),
        ],

        Recorders\SlowJobs::class => [
            'enabled' => env('PULSE_SLOW_JOBS_ENABLED', false),
            'sample_rate' => env('PULSE_SLOW_JOBS_SAMPLE_RATE', 1),
            'threshold' => env('PULSE_SLOW_JOBS_THRESHOLD', 1000),
            'ignore' => [
                // '/^Package\\\\Jobs\\\\/',
            ],
        ],

        Recorders\SlowOutgoingRequests::class => [
            'enabled' => env('PULSE_SLOW_OUTGOING_REQUESTS_ENABLED', true),
            'sample_rate' => env('PULSE_SLOW_OUTGOING_REQUESTS_SAMPLE_RATE', 1),
            'threshold' => env('PULSE_SLOW_OUTGOING_REQUESTS_THRESHOLD', 1000),
            'ignore' => [
                // '#^http://127\.0\.0\.1:13714#', // Inertia SSR...
            ],
            'groups' => [
                // '#^https://api\.github\.com/repos/.*$#' => 'api.github.com/repos/*',
                // '#^https?://([^/]*).*$#' => '\1',
                // '#/\d+#' => '/*',
            ],
        ],

        Recorders\SlowQueries::class => [
            'enabled' => env('PULSE_SLOW_QUERIES_ENABLED', true),
            'sample_rate' => env('PULSE_SLOW_QUERIES_SAMPLE_RATE', 1),
            'threshold' => env('PULSE_SLOW_QUERIES_THRESHOLD', 1000),
            'location' => env('PULSE_SLOW_QUERIES_LOCATION', true),
            'max_query_length' => env('PULSE_SLOW_QUERIES_MAX_QUERY_LENGTH', 1000),
            'ignore' => [
                '/(["`])pulse_[\w]+?\1/', // Pulse tables...
            ],
        ],

        Recorders\SlowRequests::class => [
            'enabled' => env('PULSE_SLOW_REQUESTS_ENABLED', true),
            'sample_rate' => env('PULSE_SLOW_REQUESTS_SAMPLE_RATE', 1),
            'threshold' => env('PULSE_SLOW_REQUESTS_THRESHOLD', 500),
            'ignore' => [
                '#^/'.env('PULSE_PATH', 'pulse').'#', // Pulse dashboard and its polling...
                '#^/livewire#', // Livewire round-trips...
                '#^/up$#', // Health check...
            ],
        ],

        Recorders\UserJobs::class => [
            'enabled' => env('PULSE_USER_JOBS_ENABLED', false),
            'sample_rate' => env('PULSE_USER_JOBS_SAMPLE_RATE', 1),
            'ignore' => [
                // '/^Package\\\\Jobs\\\\/',
            ],
        ],

        Recorders\UserRequests::class => [
            'enabled' => env('PULSE_USER_REQUESTS_ENABLED', true),
            'sample_rate' => env('PULSE_USER_REQUESTS_SAMPLE_RATE', 1),
            'ignore' => [
                '#^/'.env('PULSE_PATH', 'pulse').'#', // Pulse dashboard and its polling...
                '#^/livewire#', // Livewire round-trips...
                '#^/up$#', // Health check...
            ],
        ],
    ],
];
