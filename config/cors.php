<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    "paths" => [

        "*",
    ],

    "allowed_methods" => [

        "*",
    ],

    "allowed_origins" => (static function (): array {
        $raw = trim((string) env("CORS_ORIGIN", ""));

        if ($raw === "") {
            return array_values(array_filter([ env("FRONTEND_URL"), ]));
        }

        $origins = str_starts_with($raw, "[")
            ? (array) json_decode($raw, true)
            : explode(",", $raw);

        return array_values(array_filter(array_map(
            static fn (mixed $origin): string => trim((string) $origin),
            $origins,
        )));
    })(),

    "allowed_origins_patterns" => [

        //
    ],

    "allowed_headers" => [

        "*",
    ],

    "exposed_headers" => [

        //
    ],

    "max_age" => 0,

    "supports_credentials" => true,

];
