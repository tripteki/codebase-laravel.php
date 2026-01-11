<?php

namespace Src\V1\Api\Acl\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Src\V1\Api\Acl\Enums\RoleEnum;

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
        $this->registerPermissions();
    }

    /**
     * @return void
     */
    protected function registerPermissions(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole(RoleEnum::SUPERADMIN->value) ? true : null;
        });
    }
}
