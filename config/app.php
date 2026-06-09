<?php

return [

    "name" => env("APP_NAME", "Basecode"),
    "version" => env("APP_VERSION", @json_decode(file_get_contents(base_path("composer.json")), JSON_PRETTY_PRINT)["version"]),

    "url" => (function () {
        $url = env("APP_URL");
        if (! is_string($url) || $url === "") {
            return "";
        }
        if (! preg_match('/^https?:\/\//', $url)) {
            $url = "http://".$url;
        }

        return $url;
    })(),

    "email_server" => (function () {
        $override = env("APP_EMAIL_SERVER");
        if (is_string($override) && $override !== "") {
            return $override;
        }
        $url = env("APP_URL");
        if (! is_string($url) || $url === "") {
            return "";
        }
        if (! preg_match("/^https?:\/\//", $url)) {
            $url = "http://".$url;
        }
        $host = parse_url($url, PHP_URL_HOST);

        return is_string($host) && $host !== "" ? $host : "";
    })(),

    "frontend_url" => env("FRONTEND_URL"),

    "asset_url" => env("ASSET_URL", null),

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
