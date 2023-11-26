<?php

return [

    "default" => env("MAIL_MAILER", "smtp"),

    "mailers" => [

        "failover" => [

            "transport" => "failover",
            "mailers" => ["smtp", "log"],
        ],

        "smtp" => [

            "transport" => "smtp",
            "host" => "smtp.mailgun.org",
            "port" => 587,
            "encryption" => "tls",
            "username" => "",
            "password" => "",
            "timeout" => null,
            "local_domain" => "",
        ],

        "sendmail" => [

            "transport" => "sendmail",
            "path" => "/usr/sbin/sendmail -bs -i",
        ],

        "log" => [

            "transport" => "log",
            "channel" => "",
        ],

        "array" => [

            "transport" => "array",
        ],
    ],

    "from" => [

        "address" => env("MAIL_ADMIN_ADDRESS", "admin@mail.com"),
        "name" => env("MAIL_ADMIN_NAME", "Admin"),
    ],

    "markdown" => [

        "theme" => "default",
        "paths" => [

            resource_path("views/vendor/mail"),
        ],
    ],

];
