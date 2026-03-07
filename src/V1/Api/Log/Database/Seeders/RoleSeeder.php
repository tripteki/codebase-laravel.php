<?php

namespace Src\V1\Api\Log\Database\Seeders;

use Illuminate\Database\Seeder;
use Src\V1\Api\Acl\Enums\GuardEnum;
use Src\V1\Api\Acl\Enums\RoleEnum;
use Src\V1\Api\Acl\Models\Role;
use Src\V1\Api\Log\Enums\PermissionEnum as LogPermissionEnum;

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
            LogPermissionEnum::ACTIVITY_VIEW->value,
            LogPermissionEnum::ACTIVITY_DELETE->value,
        ]);

        $speaker = Role::where("name", RoleEnum::SPEAKER->value)->where("guard_name", $guard)->first();
        $speaker?->givePermissionTo([
            LogPermissionEnum::ACTIVITY_VIEW->value,
        ]);

        $delegate = Role::where("name", RoleEnum::DELEGATE->value)->where("guard_name", $guard)->first();
        $delegate?->givePermissionTo([
            LogPermissionEnum::ACTIVITY_VIEW->value,
        ]);

        $exhibitor = Role::where("name", RoleEnum::EXHIBITOR->value)->where("guard_name", $guard)->first();
        $exhibitor?->givePermissionTo([
            LogPermissionEnum::ACTIVITY_VIEW->value,
        ]);

        $sponsor = Role::where("name", RoleEnum::SPONSOR->value)->where("guard_name", $guard)->first();
        $sponsor?->givePermissionTo([
            LogPermissionEnum::ACTIVITY_VIEW->value,
        ]);

        $visitor = Role::where("name", RoleEnum::VISITOR->value)->where("guard_name", $guard)->first();
        $visitor?->givePermissionTo([
            LogPermissionEnum::ACTIVITY_VIEW->value,
        ]);
    }
}
