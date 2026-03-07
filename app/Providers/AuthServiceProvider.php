<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Modules\User\App\Enums\PermissionEnum as UserPermissionEnum;
use Modules\Acl\App\Enums\PermissionEnum as AclPermissionEnum;
use Modules\Log\App\Enums\PermissionEnum as LogPermissionEnum;
use Modules\Notification\App\Enums\PermissionEnum as NotificationPermissionEnum;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        $this->registerUserPermissions();
        $this->registerAclPermissions();
        $this->registerLogPermissions();
        $this->registerNotificationPermissions();
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
    protected function registerNotificationPermissions(): void
    {
        foreach (NotificationPermissionEnum::cases() as $permission) {
            Gate::define($permission->value, function (User $user) use ($permission) {
                return $user->hasPermissionTo($permission->value);
            });
        }
    }
}
