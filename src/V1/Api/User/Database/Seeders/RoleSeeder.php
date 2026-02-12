<?php

namespace Src\V1\Api\User\Database\Seeders;

use Src\V1\Api\User\Enums\PermissionEnum;
use Src\V1\Api\Acl\Enums\RoleEnum;
use Src\V1\Api\Acl\Enums\GuardEnum;
use Src\V1\Api\Acl\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $guard = GuardEnum::WEB->value;

        $superAdmin = Role::where("name", RoleEnum::SUPERADMIN->value)->where("guard_name", $guard)->first();
        $superAdmin?->givePermissionTo(array_column(PermissionEnum::cases(), "value"));

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
            PermissionEnum::USER_CREATE->value,
            PermissionEnum::USER_UPDATE->value,
            PermissionEnum::USER_EXPORT->value,
        ]);

        $guest = Role::where("name", RoleEnum::GUEST->value)->where("guard_name", $guard)->first();
        $guest?->givePermissionTo([

            PermissionEnum::USER_VIEW->value,
        ]);
    }
}
