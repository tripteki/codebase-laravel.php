<?php

return [

    "default" => env("MAIL_DRIVER", "smtp"),

    "mailers" => [

        "failover" => [

            "transport" => "failover",
            "mailers" => [ "smtp", "log", ],
        ],

        "smtp" => [

            "transport" => "smtp",
            "url" => env("MAIL_URL"),
            "host" => env("MAIL_HOST", "127.0.0.1"),
            "port" => env("MAIL_PORT", 2525),
            "encryption" => env("MAIL_ENCRYPTION", "tls"),
            "username" => env("MAIL_USERNAME"),
            "password" => env("MAIL_PASSWORD"),
            "local_domain" => env("MAIL_EHLO_DOMAIN"),
            "timeout" => null,
        ],

        "sendmail" => [

            "transport" => "sendmail",
            "path" => "/usr/sbin/sendmail -bs -i",
        ],

        "log" => [

            "transport" => "log",
            "channel" => env("MAIL_LOG_CHANNEL"),
        ],

        "array" => [

            "transport" => "array",
        ],
    ],

    "from" => [

        "address" => env("MAIL_ADMIN_ADDRESS", "admin@mail.com"),
        "name" => env("MAIL_ADMIN_NAME", "Admin"),
    ],

];
