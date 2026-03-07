<?php

use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Database\Models\Tenant;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
use App\Models\Tenant as AppTenant;

$isTenancy = (bool) env("IS_TENANCY", true);
$tenancyType = strtolower((string) env("TENANCY_TYPE", "path"));

return [

    /*
    |--------------------------------------------------------------------------
    | Tenancy
    |--------------------------------------------------------------------------
    */

    "is_tenancy" => $isTenancy,
    "type" => $tenancyType,



    "central_domains" => array_filter(array_map("trim", explode(",", (string) env("CENTRAL_DOMAINS", "")))),

    "tenant_identification_middleware" => $isTenancy
        ? ($tenancyType === "subdomain"
            ? InitializeTenancyBySubdomain::class
            : InitializeTenancyByPath::class)
        : null,



    "tenant_model" => AppTenant::class,
    "id_generator" => Stancl\Tenancy\UUIDGenerator::class,

    "domain_model" => Domain::class,

    "bootstrappers" => [

        Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper::class,
        App\Bootstrappers\LogTenancyBootstrapper::class,
    ],

    "features" => [],

    "routes" => true,

    "migration_parameters" => [

        "--force" => true,
        "--path" => [ database_path("migrations/tenant"), ],
        "--realpath" => true,
    ],

    "seeder_parameters" => [

        "--class" => "DatabaseSeeder",
    ],



    "database" => [

        "central_connection" => env("DB_CONNECTION", "central"),
        "template_tenant_connection" => null,
        "prefix" => "tenant",
        "suffix" => "",

        "managers" => [

            "sqlite" => Stancl\Tenancy\TenantDatabaseManagers\SQLiteDatabaseManager::class,
            "mysql" => Stancl\Tenancy\TenantDatabaseManagers\MySQLDatabaseManager::class,
            "pgsql" => Stancl\Tenancy\TenantDatabaseManagers\PostgreSQLDatabaseManager::class,
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

        "disks" => [

            "local",
            "public",
        ],

        "root_override" => [

            "local" => "%storage_path%/app/",
            "public" => "%storage_path%/app/public/",
        ],

        "suffix_storage_path" => true,
        "asset_helper_tenancy" => false,
    ],

];
