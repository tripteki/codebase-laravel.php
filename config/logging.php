<?php

return [

    "default" => env("LOG_CHANNEL", "stack") ?: "stack",

    "deprecations" => [

        "channel" => env("LOG_DEPRECATIONS_CHANNEL", "null"),
        "trace" => false,
    ],

    "channels" => [

        "stack" => [

            "driver" => "stack",
            "channels" => array_values(array_filter(
                explode(",", env("LOG_STACK", "single,daily")),
                fn (string $channel): bool => $channel !== "",
            )) ?: ["single", "daily"],
            "ignore_exceptions" => false,
        ],

        "single" => [

            "driver" => "single",
            "path" => storage_path("logs/log.log"),
            "level" => env("LOG_LEVEL", "debug"),
            "replace_placeholders" => false,
        ],

        "daily" => [

            "driver" => "daily",
            "path" => storage_path("logs/log.log"),
            "level" => env("LOG_LEVEL", "debug"),
            "days" => 14,
            "replace_placeholders" => false,
        ],

        "syslog" => [

            "driver" => "syslog",
            "level" => env("LOG_LEVEL", "debug"),
            "replace_placeholders" => false,
        ],

        "stdout" => [

            "driver" => "monolog",
            "level" => env("LOG_LEVEL", "debug"),
            "handler" => Monolog\Handler\StreamHandler::class,
            "formatter" => "",
            "with" => [ "stream" => "php://stdout", ],
            "processors" => [ \Monolog\Processor\PsrLogMessageProcessor::class, ],
        ],

        "stderr" => [

            "driver" => "monolog",
            "level" => env("LOG_LEVEL", "debug"),
            "handler" => Monolog\Handler\StreamHandler::class,
            "formatter" => "",
            "with" => [ "stream" => "php://stderr", ],
            "processors" => [ \Monolog\Processor\PsrLogMessageProcessor::class, ],
        ],
    ],

];
