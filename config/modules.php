<?php

use Nwidart\Modules\Activators\FileActivator;
use Nwidart\Modules\Providers\ConsoleServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Module Namespace
    |--------------------------------------------------------------------------
    */
    "namespace" => "Modules",

    /*
    |--------------------------------------------------------------------------
    | Module Stubs
    |--------------------------------------------------------------------------
    */
    "stubs" => [

        "enabled" => false,
        "path" => base_path("vendor/nwidart/laravel-modules/src/Commands/stubs"),
        "files" => [

            "routes/web" => "routes/web.php",
            "routes/api" => "routes/api.php",
            "composer" => "composer.json",
            "scaffold/config" => "config/config.php",
        ],
        "replacements" => [

            "routes/web" => ["LOWER_NAME", "STUDLY_NAME", "MODULE_NAMESPACE", "CONTROLLER_NAMESPACE",],
            "routes/api" => ["LOWER_NAME", "STUDLY_NAME",],
            "scaffold/config" => ["STUDLY_NAME",],
            "composer" => [

                "LOWER_NAME",
                "STUDLY_NAME",
                "VENDOR",
                "AUTHOR_NAME",
                "AUTHOR_EMAIL",
                "MODULE_NAMESPACE",
                "PROVIDER_NAMESPACE",
            ],
        ],
        "gitkeep" => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Paths
    |--------------------------------------------------------------------------
    */
    "paths" => [

        "modules" => base_path("src"),
        "assets" => public_path("modules"),
        "migration" => base_path("database/migrations"),

        /*
        |--------------------------------------------------------------------------
        | Generator
        |--------------------------------------------------------------------------
        */
        "generator" => [

            "routes" => [

                "path" => "routes",
                "generate" => true,
            ],

            "controller" => [

                "path" => "App/Http/Controllers",
                "generate" => true,
            ],

            "provider" => [

                "path" => "App/Providers",
                "generate" => true,
            ],

            "seeder" => [

                "path" => "Database/Seeders",
                "generate" => true,
            ],

            "factory" => [

                "path" => "Database/Factories",
                "generate" => true,
            ],

            "test-feature" => [

                "path" => "tests/Feature",
                "generate" => true,
            ],

            "config" => [

                "path" => "config",
                "generate" => false,
            ],

            "migration" => [

                "path" => "Database/migrations",
                "generate" => true,
            ],

            "model" => [

                "path" => "App/Models",
                "generate" => false,
            ],

            "filter" => [

                "path" => "App/Http/Middleware",
                "generate" => false,
            ],

            "request" => [

                "path" => "App/Http/Requests",
                "generate" => false,
            ],

            "policies" => [

                "path" => "App/Policies",
                "generate" => false,
            ],

            "repository" => [

                "path" => "App/Repositories",
                "generate" => false,
            ],

            "test" => [

                "path" => "tests/Unit",
                "generate" => false,
            ],

            "command" => [

                "path" => "App/Console",
                "generate" => false,
            ],

            "channels" => [

                "path" => "App/Broadcasting",
                "generate" => false,
            ],

            "observer" => [

                "path" => "App/Observers",
                "generate" => false,
            ],

            "event" => [

                "path" => "App/Events",
                "generate" => false,
            ],

            "listener" => [

                "path" => "App/Listeners",
                "generate" => false,
            ],

            "rules" => [

                "path" => "App/Rules",
                "generate" => false,
            ],

            "jobs" => [

                "path" => "App/Jobs",
                "generate" => false,
            ],

            "emails" => [

                "path" => "App/Emails",
                "generate" => false,
            ],

            "notifications" => [

                "path" => "App/Notifications",
                "generate" => false,
            ],

            "resource" => [

                "path" => "App/resources",
                "generate" => false,
            ],

            "assets" => [

                "path" => "resources/assets",
                "generate" => false,
            ],

            "lang" => [

                "path" => "lang",
                "generate" => true,
            ],

            "views" => [

                "path" => "resources/views",
                "generate" => false,
            ],

            "component-view" => [

                "path" => "resources/views/components",
                "generate" => false,
            ],

            "component-class" => [

                "path" => "App/View/Components",
                "generate" => false,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Package Commands
    |--------------------------------------------------------------------------
    */
    "commands" => ConsoleServiceProvider::defaultCommands()
        ->merge([])
        ->toArray(),

    /*
    |--------------------------------------------------------------------------
    | Scan Path
    |--------------------------------------------------------------------------
    */
    "scan" => [

        "enabled" => false,
        "paths" => [

            base_path("vendor/*/*"),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Composer File Template
    |--------------------------------------------------------------------------
    */
    "composer" => [

        "vendor" => "tripteki",
        "author" => [

            "name" => "Trip Teknologi",
            "email" => "noreply@tripteki.com",
        ],
        "composer-output" => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    */
    "cache" => [

        "enabled" => false,
        "driver" => "file",
        "key" => "laravel-modules",
        "lifetime" => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Registration
    |--------------------------------------------------------------------------
    */
    "register" => [

        "translations" => false,
        "files" => "register",
    ],

    /*
    |--------------------------------------------------------------------------
    | Activators
    |--------------------------------------------------------------------------
    */
    "activators" => [

        "file" => [

            "class" => FileActivator::class,
            "statuses-file" => base_path("modules_statuses.json"),
            "cache-key" => "activator.installed",
            "cache-lifetime" => 604800,
        ],
    ],

    "activator" => "file",

];
