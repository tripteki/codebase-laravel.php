<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Src\V1\Api\User\Enums\PermissionEnum as UserPermissionEnum;
use Src\V1\Api\Acl\Enums\PermissionEnum as AclPermissionEnum;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Gate::define("viewPulse", function (User $user) {

            return ! app()->environment("production");
        });

        $this->registerUserPermissions();
        $this->registerAclPermissions();
    }

    /**
     * Register user permissions.
     *
     * @return void
     */
    protected function registerUserPermissions(): void
    {
        foreach (UserPermissionEnum::cases() as $permission) {
            Gate::define($permission->value, function (User $user) use ($permission) {
                return $user->can($permission->value);
            });
        }
    }

    /**
     * Register ACL permissions.
     *
     * @return void
     */
    protected function registerAclPermissions(): void
    {
        foreach (AclPermissionEnum::cases() as $permission) {
            Gate::define($permission->value, function (User $user) use ($permission) {
                return $user->can($permission->value);
            });
        }
    }
}
