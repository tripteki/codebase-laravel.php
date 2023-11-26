<?php

use Illuminate\Support\Str;

return [

    "default" => env("CACHE_DRIVER", "file"),

    "stores" => [

        "octane" => [

            "driver" => "octane",
        ],

        "file" => [

            "driver" => "file",
            "path" => storage_path("framework/cache/data"),
        ],

        "database" => [

            "driver" => "database",
            "table" => "caches",
            "connection" => "cache",
            "lock_connection" => "cache",
        ],

        "apc" => [

            "driver" => "apc",
        ],

        "memcached" => [

            "driver" => "memcached",
            "persistent_id" => "",
            "sasl" => [

                "",
                "",
            ],
            "options" => [],
            "servers" => [

                ["host" => "", "port" => "", "weight" => 100],
            ],
        ],

        "redis" => [

            "driver" => "redis",
            "connection" => "cache",
            "lock_connection" => "cache",
        ],

        "array" => [

            "driver" => "array",
            "serialize" => false,
        ],
    ],

    "prefix" => Str::slug(env("APP_NAME"), "_")."_cache_",

];
