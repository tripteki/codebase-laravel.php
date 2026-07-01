<?php

namespace Modules\Notification\Database\Seeders;

use App\Support\AdminTenancySupport;
use Illuminate\Database\Seeder;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Notification\App\Enums\PermissionEnum;

class RoleSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $guard = GuardEnum::WEB->value;
        $permissions = AdminTenancySupport::currentTenantPermissions([
            PermissionEnum::NOTIFICATION_VIEW->value,
            PermissionEnum::NOTIFICATION_DELETE->value,
        ], $guard);

        foreach ([ RoleEnum::SUPERADMIN, RoleEnum::ADMIN, RoleEnum::USER, ] as $role) {
            AdminTenancySupport::currentTenantRole($role->value, $guard)?->givePermissionTo($permissions);
        }
    }
}
