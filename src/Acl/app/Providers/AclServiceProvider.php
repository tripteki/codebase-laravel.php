<?php

namespace Modules\Acl\App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\RoleEnum;

class AclServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path("Acl", "Database/migrations"));

        Gate::before(function ($user, $ability) {
            return $user->hasRole(RoleEnum::SUPERADMIN->value, GuardEnum::WEB->value) ? true : null;
        });
    }
}
