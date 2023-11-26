<?php

return [

    "default" => env("BROADCAST_DRIVER", "null"),

    "connections" => [

        "websocket" => [

            "driver" => "pusher",
            "key" => env("WEBSOCKET_KEY"),
            "secret" => env("WEBSOCKET_SECRET"),
            "app_id" => env("WEBSOCKET_ID"),
            "options" => [

                "host" => env("WEBSOCKET_HOST", "api-".env("WEBSOCKET_CLUSTER", "mt1").".pusher.com") ?: "api-".env("WEBSOCKET_CLUSTER", "mt1").".pusher.com",
                "port" => env("WEBSOCKET_PORT", 443),
                "encrypted" => true,
                "scheme" => env("WEBSOCKET_SCHEME", "https"),
                "useTLS" => env("WEBSOCKET_SCHEME", "https") === "https",
            ],
            "client_options" => [],
        ],

        "socket.io" => [

            "driver" => "redis",
            "connection" => "broadcasting",
        ],

        "log" => [

            "driver" => "log",
        ],

        "null" => [

            "driver" => "null",
        ],
    ],

];
