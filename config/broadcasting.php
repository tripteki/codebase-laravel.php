<?php

return [

    "default" => env("BROADCAST_CONNECTION", "log"),

    "connections" => [

        "pusher" => [

            "driver" => "pusher",
            "key" => env("PUSHER_APP_KEY"),
            "secret" => env("PUSHER_APP_SECRET"),
            "app_id" => env("PUSHER_APP_ID"),

            "options" => [

                "cluster" => env("PUSHER_APP_CLUSTER", "mt1"),
                "host" => env("PUSHER_HOST", env("APP_ENV") === "local" ? "127.0.0.1" : "api-".env("PUSHER_APP_CLUSTER", "mt1").".pusher.com"),
                "port" => (int) env("PUSHER_PORT", env("APP_ENV") === "local" ? 6001 : 443),
                "scheme" => env("PUSHER_SCHEME", env("APP_ENV") === "local" ? "http" : "https"),
                "useTLS" => env("PUSHER_SCHEME", env("APP_ENV") === "local" ? "http" : "https") === "https",
                "encrypted" => env("PUSHER_SCHEME", env("APP_ENV") === "local" ? "http" : "https") === "https",
            ],

            "client_options" => [

                //
            ],
        ],

        "ably" => [

            "driver" => "ably",
            "key" => env("ABLY_KEY"),
        ],

        "log" => [

            "driver" => "log",
        ],

        "null" => [

            "driver" => "null",
        ],

    ],

];
