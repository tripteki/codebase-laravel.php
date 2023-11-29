<?php

use Nwidart\Modules\Activators\FileActivator;
use Nwidart\Modules\Commands;

return [

    "namespace" => "Src",

    "paths" => [

        "modules" => base_path("src"),

        "assets" => public_path("src"),

        "migration" => base_path("database/migrations"),

        "generator" => [

            "config" => ["path" => "Config", "generate" => true],
            "command" => ["path" => "Console", "generate" => true],
            "migration" => ["path" => "Database/Migrations", "generate" => true],
            "seeder" => ["path" => "Database/Seeders", "generate" => true],
            "factory" => ["path" => "Database/Factories", "generate" => true],
            "model" => ["path" => "Models", "generate" => true],
            "routes" => ["path" => "Routes", "generate" => true],
            "filter" => ["path" => "Http/Middleware", "generate" => true],
            "controller" => ["path" => "Http/Controllers", "generate" => true],
            "request" => ["path" => "Http/Requests", "generate" => true],
            "response" => ["path" => "Http/Responses", "generate" => true],
            "provider" => ["path" => "Providers", "generate" => true],
            "lang" => ["path" => "Lang", "generate" => true],
            "assets" => ["path" => "Resources/assets", "generate" => false],
            "views" => ["path" => "Resources/views", "generate" => true],
            "test" => ["path" => "Tests", "generate" => true],
            "test-feature" => ["path" => "Tests", "generate" => false],
            "repository" => ["path" => "Repositories", "generate" => true],
            "event" => ["path" => "Events", "generate" => true],
            "listener" => ["path" => "Listeners", "generate" => true],
            "policies" => ["path" => "Policies", "generate" => true],
            "rules" => ["path" => "Rules", "generate" => false],
            "jobs" => ["path" => "Jobs", "generate" => false],
            "emails" => ["path" => "Mails", "generate" => false],
            "notifications" => ["path" => "Notifications", "generate" => true],
            "resource" => ["path" => "Transformers", "generate" => false],
            "component-view" => ["path" => "Resources/views/components", "generate" => false],
            "component-class" => ["path" => "View/Components", "generate" => false],
        ],
    ],

    "commands" => [

        Commands\CommandMakeCommand::class,
        Commands\ComponentClassMakeCommand::class,
        Commands\ComponentViewMakeCommand::class,
        Commands\ControllerMakeCommand::class,
        Commands\DisableCommand::class,
        Commands\DumpCommand::class,
        Commands\EnableCommand::class,
        Commands\EventMakeCommand::class,
        Commands\JobMakeCommand::class,
        Commands\ListenerMakeCommand::class,
        Commands\MailMakeCommand::class,
        Commands\MiddlewareMakeCommand::class,
        Commands\NotificationMakeCommand::class,
        Commands\ProviderMakeCommand::class,
        Commands\RouteProviderMakeCommand::class,
        Commands\InstallCommand::class,
        Commands\ListCommand::class,
        Commands\ModuleDeleteCommand::class,
        Commands\ModuleMakeCommand::class,
        Commands\FactoryMakeCommand::class,
        Commands\PolicyMakeCommand::class,
        Commands\RequestMakeCommand::class,
        Commands\RuleMakeCommand::class,
        Commands\MigrateCommand::class,
        Commands\MigrateFreshCommand::class,
        Commands\MigrateRefreshCommand::class,
        Commands\MigrateResetCommand::class,
        Commands\MigrateRollbackCommand::class,
        Commands\MigrateStatusCommand::class,
        Commands\MigrationMakeCommand::class,
        Commands\ModelMakeCommand::class,
        Commands\PublishCommand::class,
        Commands\PublishConfigurationCommand::class,
        Commands\PublishMigrationCommand::class,
        Commands\PublishTranslationCommand::class,
        Commands\SeedCommand::class,
        Commands\SeedMakeCommand::class,
        Commands\SetupCommand::class,
        Commands\UnUseCommand::class,
        Commands\UpdateCommand::class,
        Commands\UseCommand::class,
        Commands\ResourceMakeCommand::class,
        Commands\TestMakeCommand::class,
        Commands\LaravelModulesV6Migrator::class,
    ],

    "composer" => [

        "composer-output" => false,
    ],

    "stubs" => [

        "enabled" => false,
        "gitkeep" => false,

        "path" => base_path("vendor/tripteki/laravelphp-foundation/stubs"),

        "files" => [

            "composer" => "composer.json",
            "package" => "package.json",
            "vite" => "vite.config.js",

            "scaffold/config" => "Config/config.php",

            "routes/api" => "Routes/api.php",
            "routes/web" => "Routes/web.php",

            "views/index" => "Resources/views/index.blade.php",
            "views/master" => "Resources/views/layouts/master.blade.php",
        ],

        "replacements" => [

            "composer" => [

                "MODULE_NAMESPACE",
                "STUDLY_NAME",
            ],

            // "package" => [],

            "vite" => [

                "LOWER_NAME",
            ],

            "scaffold/config" => ["STUDLY_NAME"], "json" => ["LOWER_NAME", "STUDLY_NAME", "MODULE_NAMESPACE", "PROVIDER_NAMESPACE"],

            "routes/web" => ["LOWER_NAME", "STUDLY_NAME"],
            "routes/api" => ["LOWER_NAME"],

            "views/index" => ["LOWER_NAME"],
            "views/master" => ["LOWER_NAME", "STUDLY_NAME"],
        ],
    ],

    "scan" => [

        "enabled" => false,
        "paths" => [],
    ],

    "cache" => [

        "enabled" => false,
        "driver" => "file",
        "key" => "modules",
        "lifetime" => 60,
    ],

    "register" => [

        "translations" => true,
        "files" => "register",
    ],

    "activator" => "file",

    "activators" => [

        "file" => [

            "class" => FileActivator::class,
            "statuses-file" => base_path("src/modules_statuses.json"),
            "cache-key" => "activator.installed",
            "cache-lifetime" => 604800,
        ],
    ],

];
