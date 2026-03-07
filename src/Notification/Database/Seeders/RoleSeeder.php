<?php

namespace Modules\Notification\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Acl\App\Models\Role;
use Modules\Notification\App\Enums\PermissionEnum;

class RoleSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $guard = GuardEnum::WEB->value;

        $permissions = [
            PermissionEnum::NOTIFICATION_VIEW->value,
            PermissionEnum::NOTIFICATION_DELETE->value,
            PermissionEnum::NOTIFICATION_RESTORE->value,
        ];

        $superadmin = Role::where("name", RoleEnum::SUPERADMIN->value)->where("guard_name", $guard)->first();
        $superadmin?->givePermissionTo($permissions);

        $admin = Role::where("name", RoleEnum::ADMIN->value)->where("guard_name", $guard)->first();
        $admin?->givePermissionTo($permissions);
    }
}
