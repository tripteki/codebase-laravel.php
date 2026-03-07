<?php

use Laravel\Octane\Contracts\OperationTerminated;
use Laravel\Octane\Events\RequestHandled;
use Laravel\Octane\Events\RequestReceived;
use Laravel\Octane\Events\RequestTerminated;
use Laravel\Octane\Events\TaskReceived;
use Laravel\Octane\Events\TaskTerminated;
use Laravel\Octane\Events\TickReceived;
use Laravel\Octane\Events\TickTerminated;
use Laravel\Octane\Events\WorkerErrorOccurred;
use Laravel\Octane\Events\WorkerStarting;
use Laravel\Octane\Events\WorkerStopping;
use Laravel\Octane\Listeners\CloseMonologHandlers;
use Laravel\Octane\Listeners\CollectGarbage;
use Laravel\Octane\Listeners\DisconnectFromDatabases;
use Laravel\Octane\Listeners\EnsureUploadedFilesAreValid;
use Laravel\Octane\Listeners\EnsureUploadedFilesCanBeMoved;
use Laravel\Octane\Listeners\FlushOnce;
use Laravel\Octane\Listeners\FlushTemporaryContainerInstances;
use Laravel\Octane\Listeners\FlushUploadedFiles;
use Laravel\Octane\Listeners\ReportException;
use Laravel\Octane\Listeners\StopWorkerIfNecessary;
use Laravel\Octane\Octane;

$octaneAppUrl = (string) config("app.url");
$octaneParsed = $octaneAppUrl !== "" ? @parse_url($octaneAppUrl) : null;
$octaneScheme = is_array($octaneParsed) && isset($octaneParsed["scheme"]) ? $octaneParsed["scheme"] : "http";

return [

    "host" => is_array($octaneParsed) && isset($octaneParsed["host"]) ? $octaneParsed["host"] : "",
    "port" => is_array($octaneParsed) && isset($octaneParsed["port"]) ? (int) $octaneParsed["port"] : 80,
    "https" => $octaneScheme === "https",

    "state_file" => storage_path("logs/server-state.json"),

    "server" => "swoole",
    "garbage" => 50,
    "max_execution_time" => 30,

    "swoole" => [

        "options" => [

            "pid_file" => storage_path("logs/server/server.pid"),
            "log_file" => storage_path("logs/server.log"),

            ...($octaneScheme === "https" ? [

                "ssl_key_file" => env("SSL_KEY_FILE", ".key"),
                "ssl_cert_file" => env("SSL_CERT_FILE", ".cert"),
            
            ] : []),

            // "ssl_key_file" => ".key", //
            // "ssl_cert_file" => ".cert", //
        ],

        "ssl" => $octaneScheme === "https",
    ],

    "cache" => [

        "rows" => 1000,
        "bytes" => 10000,
    ],

    "tables" => [

        //
    ],

    "watch" => [

        "bin",
        "src",
        "app",
        "bootstrap",
        "config/**/*.php",
        "database/**/*.php",
        "public/**/*.php",
        "resources/**/*.php",
        "routes",
        "composer.lock",
        ".env",
    ],

    "listeners" => [

        WorkerStarting::class => [

            EnsureUploadedFilesAreValid::class,
            EnsureUploadedFilesCanBeMoved::class,
        ],

        RequestReceived::class => [

            ...Octane::prepareApplicationForNextOperation(),
            ...Octane::prepareApplicationForNextRequest(),
        ],

        RequestHandled::class => [

            //
        ],

        RequestTerminated::class => [

            //
        ],

        TaskReceived::class => [

            ...Octane::prepareApplicationForNextOperation(),
        ],

        TaskTerminated::class => [

            //
        ],

        TickReceived::class => [

            ...Octane::prepareApplicationForNextOperation(),
        ],

        TickTerminated::class => [

            //
        ],

        OperationTerminated::class => [

            FlushOnce::class,
            FlushTemporaryContainerInstances::class,
        ],

        WorkerErrorOccurred::class => [

            ReportException::class,
            StopWorkerIfNecessary::class,
        ],

        WorkerStopping::class => [

            CloseMonologHandlers::class,
        ],
    ],

    "warm" => [

        ...Octane::defaultServicesToWarm(),
    ],

    "flush" => [

        //
    ],

];
