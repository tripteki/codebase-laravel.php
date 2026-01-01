<?php

use Illuminate\Support\Str;

return [

    "default" => env("CACHE_DRIVER", "file"),

    "stores" => [

        "file" => [

            "driver" => "file",
            "path" => storage_path("framework/cache/data"),
            "lock_path" => storage_path("framework/cache/data"),
        ],

        "database" => [

            "driver" => "database",
            "table" => "caches",
            "connection" => env("DB_CONNECTION", "sqlite"),
            "lock_connection" => "cache",
        ],

        "redis" => [

            "driver" => "redis",
            "connection" => "cache",
            "lock_connection" => "cache",
        ],

        "octane" => [

            "driver" => "octane",
        ],

        "array" => [

            "driver" => "array",
            "serialize" => false,
        ],
    ],

    "prefix" => Str::slug(env("APP_NAME"), "_")."_cache_",

];
