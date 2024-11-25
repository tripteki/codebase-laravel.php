<?php

use Illuminate\Support\Facades\Facade;

return [

    "name" => env("APP_NAME", "Basecode"),
    "version" => env("APP_VERSION", @json_decode(file_get_contents(base_path("composer.json")), JSON_PRETTY_PRINT)["version"]),
    "url" => env("APP_URL", "http://localhost"),
    "frontend_url" => env("FRONTEND_URL", "http://frontend.localhost"),
    "asset_url" => env("ASSET_URL", ""),
    "env" => env("APP_ENV", "production"),
    "debug" => (bool) env("APP_DEBUG", false),

    "timezone" => env("APP_TIMEZONE", "UTC"),
    "locale" => env("APP_LOCALE", "en"),
    "fallback_locale" => env("APP_FALLBACK_LOCALE", "en"),
    "faker_locale" => env("APP_FAKER_LOCALE", "en_US"),

    "key" => env("APP_KEY"),
    "cipher" => "AES-256-CBC",
    "previous_keys" => [ ...array_filter(explode(",", env("APP_PREVIOUS_KEYS", ""))), ],

    /*
    | Supported: "file", "cache".
    */
    "maintenance" => [

        "driver" => env("APP_MAINTENANCE_DRIVER", "file"),
        "store" => "database",
    ],

];
