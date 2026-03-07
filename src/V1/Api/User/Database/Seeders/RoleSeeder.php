<?php

namespace Src\V1\Api\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Src\V1\Api\Acl\Enums\GuardEnum;
use Src\V1\Api\Acl\Enums\RoleEnum;
use Src\V1\Api\Acl\Models\Role;
use Src\V1\Api\User\Enums\PermissionEnum;

class RoleSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $guard = GuardEnum::WEB->value;

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

        $speaker = Role::where("name", RoleEnum::SPEAKER->value)->where("guard_name", $guard)->first();
        $speaker?->givePermissionTo([
            PermissionEnum::USER_VIEW->value,
        ]);

        $delegate = Role::where("name", RoleEnum::DELEGATE->value)->where("guard_name", $guard)->first();
        $delegate?->givePermissionTo([
            PermissionEnum::USER_VIEW->value,
        ]);

        $exhibitor = Role::where("name", RoleEnum::EXHIBITOR->value)->where("guard_name", $guard)->first();
        $exhibitor?->givePermissionTo([
            PermissionEnum::USER_VIEW->value,
        ]);

        $sponsor = Role::where("name", RoleEnum::SPONSOR->value)->where("guard_name", $guard)->first();
        $sponsor?->givePermissionTo([
            PermissionEnum::USER_VIEW->value,
        ]);

        $visitor = Role::where("name", RoleEnum::VISITOR->value)->where("guard_name", $guard)->first();
        $visitor?->givePermissionTo([
            PermissionEnum::USER_VIEW->value,
        ]);
    }
}
