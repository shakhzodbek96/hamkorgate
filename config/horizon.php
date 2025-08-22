<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Horizon Domain
    |--------------------------------------------------------------------------
    |
    | This is the subdomain where Horizon will be accessible from. If this
    | setting is null, Horizon will reside under the same domain as the
    | application. Otherwise, this value will serve as the subdomain.
    |
    */

    'domain' => env('HORIZON_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Horizon Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where Horizon will be accessible from. Feel free
    | to change this path to anything you like. Note that the URI will not
    | affect the paths of its internal API that aren't exposed to users.
    |
    */

    'path' => env('HORIZON_PATH', 'horizon'),

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Connection
    |--------------------------------------------------------------------------
    |
    | This is the name of the Redis connection where Horizon will store the
    | meta information required for it to function. It includes the list
    | of supervisors, failed jobs, job metrics, and other information.
    |
    */

    'use' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Prefix
    |--------------------------------------------------------------------------
    |
    | This prefix will be used when storing all Horizon data in Redis. You
    | may modify the prefix when you are running multiple installations
    | of Horizon on the same server so that they don't have problems.
    |
    */

    'prefix' => env(
        'HORIZON_PREFIX',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_horizon:'
    ),

    /*
    |--------------------------------------------------------------------------
    | Horizon Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will get attached onto each Horizon route, giving you
    | the chance to add your own middleware to this list or change any of
    | the existing middleware. Or, you can simply stick with this list.
    |
    */

    'middleware' => ['auth','web','admin'],

    /*
    |--------------------------------------------------------------------------
    | Queue Wait Time Thresholds
    |--------------------------------------------------------------------------
    |
    | This option allows you to configure when the LongWaitDetected event
    | will be fired. Every connection / queue combination may have its
    | own, unique threshold (in seconds) before this event is fired.
    |
    */

    'wait' => [
        'seconds' => 0,   // Set to 0 seconds wait time
        'balancing' => 1, // Check for balancing very frequently
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Trimming Times
    |--------------------------------------------------------------------------
    |
    | Here you can configure for how long (in minutes) you desire Horizon to
    | persist the recent and failed jobs. Typically, recent jobs are kept
    | for one hour while all failed jobs are stored for an entire week.
    |
    */

    'trim' => [
        'recent' => 30,
        'pending' => 2160,
        'completed' => 10,
        'recent_failed' => 60,
        'failed' => 30,
        'monitored' => 10080,
    ],

    /*
    |--------------------------------------------------------------------------
    | Silenced Jobs
    |--------------------------------------------------------------------------
    |
    | Silencing a job will instruct Horizon to not place the job in the list
    | of completed jobs within the Horizon dashboard. This setting may be
    | used to fully remove any noisy jobs from the completed jobs list.
    |
    */

    'silenced' => [
        // App\Jobs\ExampleJob::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Metrics
    |--------------------------------------------------------------------------
    |
    | Here you can configure how many snapshots should be kept to display in
    | the metrics graph. This will get used in combination with Horizon's
    | `horizon:snapshot` schedule to define how long to retain metrics.
    |
    */

    'metrics' => [
        'trim_snapshots' => [
            'job' => 64,
            'queue' => 64,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fast Termination
    |--------------------------------------------------------------------------
    |
    | When this option is enabled, Horizon's "terminate" command will not
    | wait on all of the workers to terminate unless the --wait option
    | is provided. Fast termination can shorten deployment delay by
    | allowing a new instance of Horizon to start while the last
    | instance will continue to terminate each of its workers.
    |
    */

    'fast_termination' => false,

    /*
    |--------------------------------------------------------------------------
    | Memory Limit (MB)
    |--------------------------------------------------------------------------
    |
    | This value describes the maximum amount of memory the Horizon master
    | supervisor may consume before it is terminated and restarted. For
    | configuring these limits on your workers, see the next section.
    |
    */

    'memory_limit' => 8192,

    /*
    |--------------------------------------------------------------------------
    | Queue Worker Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may define the queue worker settings used by your application
    | in all environments. These supervisors and settings handle all your
    | queued jobs and will be provisioned by Horizon during deployment.
    |
    */

    'defaults' => [
        'default' => [
            'connection' => 'redis',
            'queue' => ['default'],
            'balance' => 'auto',
            'maxProcesses' => 30,
            'minProcesses' => 5,
            'sleep' => 0,
            'tries' => 3,
            'timeout' => 360,
            'memory' => 256,
        ],
    ],

    'environments' => [
        'production' => [
            'processing' => [
                'connection' => 'redis',
                'queue' => ['processing'],
                'balance' => 'auto',
                'maxProcesses' => 40,
                'minProcesses' => 5,
                'sleep' => 0,
                'tries' => 3,
                'timeout' => 360,
                'memory' => 256,
            ],
            'webhooks' => [
                'connection' => 'redis',
                'queue' => ['webhooks'],
                'balance' => 'auto',
                'maxProcesses' => 30,
                'minProcesses' => 2,
                'tries' => 1,
                'sleep' => 0,
                'timeout' => 30,
                'memory' => 128,
            ],
            'telegram' => [
                'connection' => 'redis',
                'queue' => ['telegram'],
                'balance' => 'auto',
                'maxProcesses' => 10,
                'minProcesses' => 1,
                'tries' => 1,
                'sleep' => 0,
                'timeout' => 10,
                'memory' => 128,
            ],
            'sync' => [
                'connection' => 'redis',
                'queue' => ['sync'],
                'balance' => 'auto',
                'processes' => 1,
                'min' => 1,
                'tries' => 2,
                'memory' => 128,
                'sleep' => 0,
                'timeout' => 30
            ],
            'long' => [
                'connection' => 'redis',
                'queue' => ['long'],
                'balance' => 'auto',
                'processes' => 1,
                'min' => 1,
                'tries' => 2,
                'memory' => 128,
                'sleep' => 0,
                'timeout' => 3600
            ],
            'balance_humo' => [
                'connection' => 'redis',
                'queue' => ['balance_humo'],
                'balance' => 'auto',
                'maxProcesses' => 150,
                'minProcesses' => 10,
                'tries' => 1,
                'memory' => 200,
                'sleep' => 0,
                'timeout' => 300
            ],
            'balance_humo_gold' => [
                'connection' => 'redis',
                'queue' => ['balance_humo_gold'],
                'balance' => 'auto',
                'maxProcesses' => 150,
                'minProcesses' => 10,
                'tries' => 1,
                'memory' => 128,
                'sleep' => 0,
                'timeout' => 300
            ],
            'balance_uzcard' => [
                'connection' => 'redis',
                'queue' => ['balance_uzcard'],
                'balance' => 'auto',
                'maxProcesses' => 50,
                'minProcesses' => 2,
                'tries' => 2,
                'memory' => 128,
                'sleep' => 0,
                'timeout' => 150
            ],
            'payment_humo' => [
                'connection' => 'redis',
                'queue' => ['payment_humo'],
                'balance' => 'auto',
                'maxProcesses' => 100,
                'minProcesses' => 1,
                'tries' => 3,
                'memory' => 128,
                'sleep' => 0,
                'timeout' => 180
            ],
            'payment_uzcard' => [
                'connection' => 'redis',
                'queue' => ['payment_uzcard'],
                'balance' => 'auto',
                'maxProcesses' => 100,
                'minProcesses' => 20,
                'tries' => 3,
                'memory' => 128,
                'sleep' => 0,
                'timeout' => 120
            ],
        ],
        'local' => [
            'sync' => [
                'connection' => 'redis',
                'queue' => ['sync'],
                'balance' => 'auto',
                'processes' => 1,
                'min' => 1,
                'tries' => 2,
                'memory' => 512,
                'sleep' => 0,
                'timeout' => 120
            ],
            'import' => [
                'connection' => 'redis',
                'queue' => ['import'],
                'balance' => 'auto',
                'maxProcesses' => 3,
                'minProcesses' => 1,
                'tries' => 3,
                'memory' => 1512,
                'sleep' => 0,
                'timeout' => 120
            ],
            'default' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'auto',
                'process' => 1,
                'sleep' => 0,
                'tries' => 3,
                'timeout' => 120,
                'memory' => 512,
            ],
        ],
        'development' => [
            'webhooks' => [
                'connection' => 'redis',
                'queue' => ['webhooks'],
                'balance' => 'auto',
                'processes' => 2,
                'min' => 1,
                'tries' => 3,
                'sleep' => 0,
                'timeout' => 300
            ],
            'sync' => [
                'connection' => 'redis',
                'queue' => ['sync'],
                'balance' => 'auto',
                'processes' => 1,
                'min' => 1,
                'tries' => 2,
                'memory' => 1024,
                'sleep' => 0,
                'timeout' => 300
            ],
            'balance_humo' => [
                'connection' => 'redis',
                'queue' => ['balance_humo'],
                'balance' => 'auto',
                'processes' => 2,
                'min' => 2,
                'tries' => 1,
                'memory' => 4096,
                'sleep' => 0,
                'timeout' => 30
            ],
            'balance_uzcard' => [
                'connection' => 'redis',
                'queue' => ['balance_uzcard'],
                'balance' => 'auto',
                'processes' => 2,
                'min' => 2,
                'tries' => 1,
                'memory' => 4096,
                'sleep' => 0,
                'timeout' => 30
            ],
            'payment_humo' => [
                'connection' => 'redis',
                'queue' => ['payment_humo'],
                'balance' => 'auto',
                'processes' => 2,
                'min' => 2,
                'tries' => 1,
                'memory' => 1024,
                'sleep' => 0,
                'timeout' => 200
            ],
            'payment_uzcard' => [
                'connection' => 'redis',
                'queue' => ['payment_uzcard'],
                'balance' => 'auto',
                'processes' => 2,
                'min' => 2,
                'tries' => 1,
                'memory' => 4096,
                'sleep' => 0,
                'timeout' => 100
            ]
        ],
    ],
];
