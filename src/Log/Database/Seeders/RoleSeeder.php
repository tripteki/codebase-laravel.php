<?php

namespace Modules\Log\Database\Seeders;

use App\Support\AdminTenancySupport;
use Illuminate\Database\Seeder;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Log\App\Enums\PermissionEnum as LogPermissionEnum;

class RoleSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $guard = GuardEnum::WEB->value;
        $permissions = AdminTenancySupport::currentTenantPermissions([
            LogPermissionEnum::ACTIVITY_VIEW->value,
        ], $guard);

        foreach ([ RoleEnum::SUPERADMIN, RoleEnum::ADMIN, RoleEnum::USER, ] as $role) {
            AdminTenancySupport::currentTenantRole($role->value, $guard)?->givePermissionTo($permissions);
        }
    }
}
