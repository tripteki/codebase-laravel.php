<?php

use Illuminate\Support\Str;

return [

    /*
    | Supported: "file", "cookie", "database", "apc", "memcached", "redis", "array".
    */
    "driver" => env("SESSION_DRIVER", "file"),

    "lifetime" => 120,
    "lottery" => [ 2, 100, ],
    "expire_on_close" => false,
    "encrypt" => false,
    "partitioned" => false,



    /*
    | Driver: "file".
    */
    "files" => storage_path("framework/sessions"),

    /*
    | Driver: "cookie".
    */
    "cookie" => Str::slug(env("APP_NAME"), "_")."_session-cookie",
    "path" => "/",
    "domain" => env("SESSION_DOMAIN"),
    "same_site" => "lax",
    "http_only" => true,
    "secure" => env("SESSION_SECURE_COOKIE"),

    /*
    | Driver: "database".
    */
    "connection" => "session",
    "table" => "sessions",

    /*
    | Driver: "apc", "dynamodb", "memcached", "redis".
    */
    "store" => null,

];
