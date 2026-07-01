<?php

namespace Modules\Acl\App\Providers;

use Illuminate\Support\Facades\Event as EventDispatcher;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Acl\App\Events\PermissionAdminExported;
use Modules\Acl\App\Events\PermissionAdminExportedFailed;
use Modules\Acl\App\Events\PermissionAdminImported;
use Modules\Acl\App\Events\PermissionAdminImportedFailed;
use Modules\Acl\App\Events\RoleAdminExported;
use Modules\Acl\App\Events\RoleAdminExportedFailed;
use Modules\Acl\App\Events\RoleAdminImported;
use Modules\Acl\App\Events\RoleAdminImportedFailed;
use Modules\Acl\App\Listeners\AclAdminOperationDatabaseListener;
use Modules\Acl\App\Models\Permission;
use Modules\Acl\App\Models\Role;
use Modules\Acl\App\Policies\PermissionPolicy;
use Modules\Acl\App\Policies\RolePolicy;

class AclServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path("Acl", "Database/migrations"));
        $this->registerTranslations();

        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);

        Gate::before(function ($user, $ability) {
            if (! $user instanceof \App\Models\User) {
                return null;
            }

            return is_central_superadmin($user) ? true : null;
        });

        $databaseListener = AclAdminOperationDatabaseListener::class;

        EventDispatcher::listen(RoleAdminImported::class, [$databaseListener, "handleRoleImported"]);
        EventDispatcher::listen(RoleAdminImportedFailed::class, [$databaseListener, "handleRoleImportedFailed"]);
        EventDispatcher::listen(RoleAdminExported::class, [$databaseListener, "handleRoleExported"]);
        EventDispatcher::listen(RoleAdminExportedFailed::class, [$databaseListener, "handleRoleExportedFailed"]);
        EventDispatcher::listen(PermissionAdminImported::class, [$databaseListener, "handlePermissionImported"]);
        EventDispatcher::listen(PermissionAdminImportedFailed::class, [$databaseListener, "handlePermissionImportedFailed"]);
        EventDispatcher::listen(PermissionAdminExported::class, [$databaseListener, "handlePermissionExported"]);
        EventDispatcher::listen(PermissionAdminExportedFailed::class, [$databaseListener, "handlePermissionExportedFailed"]);
    }

    /**
     * @return void
     */
    protected function registerTranslations(): void
    {
        $langPath = module_path("Acl", "lang");

        if (! is_dir($langPath)) {
            return;
        }

        $translator = $this->app["translator"];

        foreach (glob($langPath."/*", GLOB_ONLYDIR) ?: [] as $localePath) {
            $locale = basename($localePath);

            foreach (glob($localePath."/*.php") ?: [] as $file) {
                $group = basename($file, ".php");
                $lines = require $file;
                $prefixed = [];

                $this->flattenTranslations($lines, "{$group}.", $prefixed);
                $translator->addLines($prefixed, $locale);
            }
        }
    }

    /**
     * @param array<string, mixed> $lines
     * @param string $prefix
     * @param array<string, mixed> $prefixed
     * @return void
     */
    protected function flattenTranslations(array $lines, string $prefix, array &$prefixed): void
    {
        foreach ($lines as $key => $value) {
            if (is_array($value)) {
                $this->flattenTranslations($value, $prefix.$key.".", $prefixed);

                continue;
            }

            $prefixed[$prefix.$key] = $value;
        }
    }
}
