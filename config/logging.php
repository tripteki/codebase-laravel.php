<?php

return [

    "default" => env("LOG_CHANNEL", "stack"),

    "deprecations" => [

        "channel" => env("LOG_DEPRECATIONS_CHANNEL", "null"),
        "trace" => false,
    ],

    "channels" => [

        "stack" => [

            "driver" => "stack",
            "channels" => ["single", "daily"],
            "ignore_exceptions" => false,
        ],

        "single" => [

            "driver" => "single",
            "path" => storage_path("logs/log.log"),
            "level" => env("LOG_LEVEL", "debug"),
        ],

        "daily" => [

            "driver" => "daily",
            "path" => storage_path("logs/log.log"),
            "level" => env("LOG_LEVEL", "debug"),
            "days" => 14,
        ],

        "emergency" => [

            "path" => storage_path("logs/log.log"),
        ],

        "errorlog" => [

            "driver" => "errorlog",
            "level" => env("LOG_LEVEL", "debug"),
        ],

        "syslog" => [

            "driver" => "syslog",
            "level" => env("LOG_LEVEL", "debug"),
        ],

        "stderr" => [

            "driver" => "monolog",
            "level" => env("LOG_LEVEL", "debug"),
            "handler" => Monolog\Handler\StreamHandler::class,
            "formatter" => "",
            "with" => ["stream" => "php://stderr"],
        ],

        "null" => [

            "driver" => "monolog",
            "handler" => Monolog\Handler\NullHandler::class,
        ],
    ],

];
