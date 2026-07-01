<?php

use Modules\Event\App\Models\Event;
use Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper;
use Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper;
use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
use Stancl\Tenancy\TenantDatabaseManagers\MySQLDatabaseManager;
use Stancl\Tenancy\TenantDatabaseManagers\PostgreSQLDatabaseManager;
use Stancl\Tenancy\TenantDatabaseManagers\SQLiteDatabaseManager;

return [

    "type" => "path",

    "central_domains" => array_filter(array_map("trim", explode(",", (string) env("CENTRAL_DOMAINS", "localhost,127.0.0.1")))),

    "tenant_identification_middleware" => InitializeTenancyByPath::class,

    "tenant_model" => Event::class,
    "id_generator" => null,

    "domain_model" => Domain::class,

    "bootstrappers" => [
        CacheTenancyBootstrapper::class,
        QueueTenancyBootstrapper::class,
    ],

    "features" => [],

    "routes" => false,

    "migration_parameters" => [
        "--force" => true,
        "--realpath" => true,
    ],

    "seeder_parameters" => [
        "--class" => "Database\\Seeders\\DatabaseSeeder",
    ],

    "database" => [
        "central_connection" => env("DB_CONNECTION", "mysql"),
        "template_tenant_connection" => null,
        "prefix" => "tenant",
        "suffix" => "",
        "managers" => [
            "sqlite" => SQLiteDatabaseManager::class,
            "mysql" => MySQLDatabaseManager::class,
            "pgsql" => PostgreSQLDatabaseManager::class,
        ],
    ],

    "cache" => [
        "tag_base" => "tenant",
    ],

    "redis" => [
        "prefix_base" => "tenant",
        "prefixed_connections" => [],
    ],

    "filesystem" => [
        "suffix_base" => "tenant",
        "disks" => [],
        "root_override" => [],
        "suffix_storage_path" => false,
        "asset_helper_tenancy" => false,
    ],

];
