<?php

namespace Src\V1\Api\Log\Database\Seeders;

use Src\V1\Api\Log\Enums\PermissionEnum as LogPermissionEnum;
use Src\V1\Api\Acl\Enums\GuardEnum;
use Src\V1\Api\Acl\Enums\RoleEnum;
use Src\V1\Api\Acl\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $superadmin = Role::where("name", RoleEnum::SUPERADMIN->value)->where("guard_name", GuardEnum::WEB->value)->first();

        if ($superadmin) {

            $superadmin->givePermissionTo([

                LogPermissionEnum::ACTIVITY_VIEW->value,
                LogPermissionEnum::ACTIVITY_DELETE->value,
            ]);
        }

        $admin = Role::where("name", RoleEnum::ADMIN->value)->where("guard_name", GuardEnum::WEB->value)->first();

        if ($admin) {

            $admin->givePermissionTo([

                LogPermissionEnum::ACTIVITY_VIEW->value,
            ]);
        }

        $user = Role::where("name", RoleEnum::USER->value)->where("guard_name", GuardEnum::WEB->value)->first();

        if ($user) {

            $user->givePermissionTo([

                LogPermissionEnum::ACTIVITY_VIEW->value,
            ]);
        }
    }
}
