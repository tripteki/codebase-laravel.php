<?php

return [

    "defaults" => [

        "guard" => env("AUTH_GUARD", "web"),
        "passwords" => "users_eloquent",
    ],

    /*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    */

    "guards" => [

        "api" => [

            "driver" => "jwt",
            "provider" => "users_eloquent",
        ],

        "web" => [

            "driver" => "session",
            "provider" => "users_eloquent",
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Reset
    |--------------------------------------------------------------------------
    */

    "passwords" => [

        "users_database" => [

            "provider" => "users_database",
            "table" => "password_reset_tokens",
            "expire" => 60,
            "throttle" => 60,
        ],

        "users_eloquent" => [

            "provider" => "users_eloquent",
            "table" => "password_reset_tokens",
            "expire" => 60,
            "throttle" => 60,
        ],
    ],

    "password_timeout" => 10800,

    /*
    | Supported: "database", "eloquent".
    */
    "providers" => [

        "users_database" => [

            "driver" => "database",
            "table" => "users",
        ],

        "users_eloquent" => [

            "driver" => "eloquent",
            "model" => App\Models\User::class,
        ],
    ],

];
