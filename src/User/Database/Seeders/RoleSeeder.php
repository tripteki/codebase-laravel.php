<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Acl\App\Models\Role;
use Modules\User\App\Enums\PermissionEnum;

class RoleSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $guard = GuardEnum::WEB->value;

        $superadmin = Role::where("name", RoleEnum::SUPERADMIN->value)->where("guard_name", $guard)->first();
        $superadmin?->givePermissionTo([
            PermissionEnum::USER_VIEW->value,
            PermissionEnum::USER_CREATE->value,
            PermissionEnum::USER_UPDATE->value,
            PermissionEnum::USER_DELETE->value,
            PermissionEnum::USER_RESTORE->value,
            PermissionEnum::USER_IMPORT->value,
            PermissionEnum::USER_EXPORT->value,
        ]);

        $admin = Role::where("name", RoleEnum::ADMIN->value)->where("guard_name", $guard)->first();
        $admin?->givePermissionTo([
            PermissionEnum::USER_VIEW->value,
            PermissionEnum::USER_CREATE->value,
            PermissionEnum::USER_UPDATE->value,
            PermissionEnum::USER_DELETE->value,
            PermissionEnum::USER_RESTORE->value,
            PermissionEnum::USER_IMPORT->value,
            PermissionEnum::USER_EXPORT->value,
        ]);

        $user = Role::where("name", RoleEnum::USER->value)->where("guard_name", $guard)->first();
        $user?->givePermissionTo([
            PermissionEnum::USER_VIEW->value,
        ]);

        $guest = Role::where("name", RoleEnum::GUEST->value)->where("guard_name", $guard)->first();
        $guest?->givePermissionTo([
            PermissionEnum::USER_VIEW->value,
        ]);
    }
}
