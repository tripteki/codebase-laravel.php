<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Src\V1\Api\User\Enums\PermissionEnum as UserPermissionEnum;
use Src\V1\Api\Acl\Enums\PermissionEnum as AclPermissionEnum;
use Src\V1\Api\Log\Enums\PermissionEnum as LogPermissionEnum;
use App\Enum\Tenant\PermissionEnum as TenantPermissionEnum;
use App\Enum\Stage\StageMeetingPermissionEnum;
use App\Enum\Stage\StageSessionPermissionEnum;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        Gate::define("viewPulse", function (User $user) {

            return ! app()->environment("production");
        });

        $this->registerUserPermissions();
        $this->registerAclPermissions();
        $this->registerLogPermissions();
        $this->registerTenantPermissions();
        $this->registerStageMeetingPermissions();
        $this->registerStageSessionPermissions();
    }

    /**
     * @return void
     */
    protected function registerUserPermissions(): void
    {
        foreach (UserPermissionEnum::cases() as $permission) {
            Gate::define($permission->value, function (User $user) use ($permission) {
                return $user->hasPermissionTo($permission->value);
            });
        }
    }

    /**
     * @return void
     */
    protected function registerAclPermissions(): void
    {
        foreach (AclPermissionEnum::cases() as $permission) {
            Gate::define($permission->value, function (User $user) use ($permission) {
                return $user->hasPermissionTo($permission->value);
            });
        }
    }

    /**
     * @return void
     */
    protected function registerLogPermissions(): void
    {
        foreach (LogPermissionEnum::cases() as $permission) {
            Gate::define($permission->value, function (User $user) use ($permission) {
                return $user->hasPermissionTo($permission->value);
            });
        }
    }

    /**
     * @return void
     */
    protected function registerTenantPermissions(): void
    {
        foreach (TenantPermissionEnum::cases() as $permission) {
            Gate::define($permission->value, function (User $user) use ($permission) {
                return is_central() && $user->hasPermissionTo($permission->value);
            });
        }
    }

    /**
     * @return void
     */
    protected function registerStageMeetingPermissions(): void
    {
        foreach (StageMeetingPermissionEnum::cases() as $permission) {
            Gate::define($permission->value, function (User $user) use ($permission) {
                return is_central() && $user->hasPermissionTo($permission->value);
            });
        }
    }

    /**
     * @return void
     */
    protected function registerStageSessionPermissions(): void
    {
        foreach (StageSessionPermissionEnum::cases() as $permission) {
            Gate::define($permission->value, function (User $user) use ($permission) {
                return is_central() && $user->hasPermissionTo($permission->value);
            });
        }
    }
}
