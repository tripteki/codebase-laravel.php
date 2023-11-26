<?php

return [

    /*
    | Supported: "bcrypt", "argon", "argon2id"
    */
    "driver" => "bcrypt",

    /*
    | Driver: "bcrypt"
    */
    "bcrypt" => [

        "rounds" => 10,
    ],

    /*
    | Driver: "argon", "argon2id"
    */
    "argon" => [

        "memory" => 65536,
        "threads" => 1,
        "time" => 4,
    ],

];
