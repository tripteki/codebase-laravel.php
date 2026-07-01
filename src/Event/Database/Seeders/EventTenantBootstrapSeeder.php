<?php

namespace Modules\Event\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Acl\Database\Seeders\PermissionSeeder as AclPermissionSeeder;
use Modules\Acl\Database\Seeders\RoleSeeder as AclRoleSeeder;
use Modules\Event\App\Enums\AddOnEnum;
use Modules\Event\App\Models\Event;
use Modules\Event\App\Support\AddOnsHelper;
use Modules\Log\Database\Seeders\PermissionSeeder as LogPermissionSeeder;
use Modules\Log\Database\Seeders\RoleSeeder as LogRoleSeeder;
use Modules\Notification\Database\Seeders\PermissionSeeder as NotificationPermissionSeeder;
use Modules\Notification\Database\Seeders\RoleSeeder as NotificationRoleSeeder;
use Modules\User\Database\Seeders\CreateTenantAdminUserSeeder;
use Modules\User\Database\Seeders\PermissionSeeder as UserPermissionSeeder;
use Modules\User\Database\Seeders\RoleSeeder as UserRoleSeeder;
use Modules\User\Database\Seeders\SyncTenantBrandingSettingsSeeder;

class EventTenantBootstrapSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $event = tenant();

        if (! $event instanceof Event) {
            return;
        }

        $modules = AddOnsHelper::moduleValues($event);

        if (
            in_array(AddOnEnum::MODULES_USER->value, $modules, true)
            || in_array(AddOnEnum::MODULES_ACL->value, $modules, true)
        ) {
            $this->call([
                AclRoleSeeder::class,
            ]);
        }

        if (in_array(AddOnEnum::MODULES_ACL->value, $modules, true)) {
            $this->call([
                AclPermissionSeeder::class,
            ]);
        }

        if (in_array(AddOnEnum::MODULES_USER->value, $modules, true)) {
            $this->call([
                UserPermissionSeeder::class,
                UserRoleSeeder::class,
                CreateTenantAdminUserSeeder::class,
            ]);
        }

        if (in_array(AddOnEnum::MODULES_LOG->value, $modules, true)) {
            $this->call([
                LogPermissionSeeder::class,
                LogRoleSeeder::class,
            ]);
        }

        if (in_array(AddOnEnum::MODULES_NOTIFICATION->value, $modules, true)) {
            $this->seedModule(AddOnEnum::MODULES_NOTIFICATION);
        }

        $this->call([
            SyncTenantBrandingSettingsSeeder::class,
        ]);
    }

    /**
     * @param AddOnEnum $module
     * @return void
     */
    public function seedModule(AddOnEnum $module): void
    {
        match ($module) {
            AddOnEnum::MODULES_ACL => $this->call([
                AclRoleSeeder::class,
                AclPermissionSeeder::class,
            ]),
            AddOnEnum::MODULES_USER => $this->call([
                AclRoleSeeder::class,
                UserPermissionSeeder::class,
                UserRoleSeeder::class,
                CreateTenantAdminUserSeeder::class,
            ]),
            AddOnEnum::MODULES_LOG => $this->call([
                LogPermissionSeeder::class,
                LogRoleSeeder::class,
            ]),
            AddOnEnum::MODULES_NOTIFICATION => $this->call([
                NotificationPermissionSeeder::class,
                NotificationRoleSeeder::class,
            ]),
            default => null,
        };
    }
}
