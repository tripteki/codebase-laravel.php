<?php

return [

    "default" => env("BROADCAST_DRIVER", "null"),

    "connections" => [

        "websocket" => [

            "driver" => "pusher",
            "app_id" => env("WEBSOCKET_ID"),
            "key" => env("WEBSOCKET_KEY"),
            "secret" => env("WEBSOCKET_SECRET"),
            "options" => [

                "cluster" => env("WEBSOCKET_CLUSTER", "mt1"),
                "host" => env("WEBSOCKET_HOST", "127.0.0.1"),
                "port" => env("WEBSOCKET_PORT", 6001),
                "scheme" => env("WEBSOCKET_SCHEME"),
                "encrypted" => true,
            ],
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
