<?php

return [

    /*
     * Route prefix.
     *
     * Default global prefix "/api" scope.
     */
    "route" => [

        /*
        * Route prefix for admin scope.
        */
        "admin" => "admin",

        /*
        * Route prefix for user scope.
        */
        "user" => null, // "auth:web" //
    ],

    /*
     * Middleware.
     */
    "middleware" => [

        /*
        * Middleware for admin scope.
        */
        "admin" => [ "api", ],

        /*
        * Middleware for user scope.
        */
        "user" => [ "api", "auth:api", ],
    ],
];
