<?php

namespace Modules\Log\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Acl\App\Models\Role;
use Modules\Log\App\Enums\PermissionEnum as LogPermissionEnum;

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
        ]);

        $user = Role::where("name", RoleEnum::USER->value)->where("guard_name", $guard)->first();
        $user?->givePermissionTo([
            LogPermissionEnum::ACTIVITY_VIEW->value,
        ]);

        $guest = Role::where("name", RoleEnum::GUEST->value)->where("guard_name", $guard)->first();
        $guest?->givePermissionTo([
            LogPermissionEnum::ACTIVITY_VIEW->value,
        ]);
    }
}
