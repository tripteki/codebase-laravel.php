<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    */

    "default" => env("DB_CONNECTION", "sqlite"),

    "migrations" => "migrations",

    "connections" => [

        "sqlite" => [

            "driver" => "sqlite",
            "url" => env("DATABASE_URL"),
            "database" => env("DB_DATABASE", database_path("database.sqlite")),
            "prefix" => "",
            "foreign_key_constraints" => true,
            "busy_timeout" => null,
            "journal_mode" => null,
        ],

        "mariadb" => [

            "driver" => "mariadb",
            "url" => env("DATABASE_URL"),
            "host" => env("DB_HOST", "localhost"),
            "port" => env("DB_PORT", "3306"),
            "database" => env("DB_DATABASE", "forge"),
            "username" => env("DB_USERNAME", "forge"),
            "password" => env("DB_PASSWORD", ""),
            "unix_socket" => "",
            "charset" => "utf8mb4",
            "collation" => "utf8mb4_unicode_ci",
            "prefix" => "",
            "prefix_indexes" => true,
            "strict" => true,
            "engine" => null,
            "options" => extension_loaded("pdo_mysql") ? array_filter([ PDO::MYSQL_ATTR_SSL_CA => "", ]) : [],
        ],

        "mysql" => [

            "driver" => "mysql",
            "url" => env("DATABASE_URL"),
            "host" => env("DB_HOST", "localhost"),
            "port" => env("DB_PORT", "3306"),
            "database" => env("DB_DATABASE", "forge"),
            "username" => env("DB_USERNAME", "forge"),
            "password" => env("DB_PASSWORD", ""),
            "unix_socket" => "",
            "charset" => "utf8mb4",
            "collation" => "utf8mb4_unicode_ci",
            "prefix" => "",
            "prefix_indexes" => true,
            "strict" => true,
            "engine" => null,
            "options" => extension_loaded("pdo_mysql") ? array_filter([ PDO::MYSQL_ATTR_SSL_CA => "", ]) : [],
        ],

        "pgsql" => [

            "driver" => "pgsql",
            "url" => env("DATABASE_URL"),
            "host" => env("DB_HOST", "localhost"),
            "port" => env("DB_PORT", "5432"),
            "database" => env("DB_DATABASE", "forge"),
            "username" => env("DB_USERNAME", "forge"),
            "password" => env("DB_PASSWORD", ""),
            "charset" => "utf8",
            "prefix" => "",
            "prefix_indexes" => true,
            "search_path" => "public",
            "sslmode" => "prefer",
        ],

        "sqlsrv" => [

            "driver" => "sqlsrv",
            "url" => env("DATABASE_URL"),
            "host" => env("DB_HOST", "localhost"),
            "port" => env("DB_PORT", "1433"),
            "database" => env("DB_DATABASE", "forge"),
            "username" => env("DB_USERNAME", "forge"),
            "password" => env("DB_PASSWORD", ""),
            "charset" => "utf8",
            "prefix" => "",
            "prefix_indexes" => true,
            "encrypt" => "yes",
            "trust_server_certificate" => "false",
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Memory
    |--------------------------------------------------------------------------
    */

    "redis" => [

        "client" => env("MM_CLIENT", "phpredis"),

        "options" => [

            "cluster" => "redis",
            "prefix" => Str::slug(env("APP_NAME"), "_")."_database_",
        ],

        "default" => [

            "url" => env("MEMORY_URL"),
            "host" => env("MM_HOST", "localhost"),
            "username" => env("MM_USERNAME"),
            "password" => env("MM_PASSWORD"),
            "port" => env("MM_PORT", "6379"),
            "database" => env("MM_DATABASE_DEFAULT", "0"),
        ],

        "cache" => [

            "url" => env("MEMORY_URL"),
            "host" => env("MM_HOST", "localhost"),
            "username" => env("MM_USERNAME"),
            "password" => env("MM_PASSWORD"),
            "port" => env("MM_PORT", "6379"),
            "database" => env("MM_DATABASE_CACHE", "1"),
        ],

        "session" => [

            "url" => env("MEMORY_URL"),
            "host" => env("MM_HOST", "localhost"),
            "username" => env("MM_USERNAME"),
            "password" => env("MM_PASSWORD"),
            "port" => env("MM_PORT", "6379"),
            "database" => env("MM_DATABASE_SESSION", "2"),
        ],

        "queue" => [

            "url" => env("MEMORY_URL"),
            "host" => env("MM_HOST", "localhost"),
            "username" => env("MM_USERNAME"),
            "password" => env("MM_PASSWORD"),
            "port" => env("MM_PORT", "6379"),
            "database" => env("MM_DATABASE_QUEUE", "3"),
        ],

        "broadcasting" => [

            "url" => env("MEMORY_URL"),
            "host" => env("MM_HOST", "localhost"),
            "username" => env("MM_USERNAME"),
            "password" => env("MM_PASSWORD"),
            "port" => env("MM_PORT", "6379"),
            "database" => env("MM_DATABASE_BROADCASTING", "4"),
        ],
    ],

];
