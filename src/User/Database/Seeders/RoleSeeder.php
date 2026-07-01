<?php

namespace Modules\User\Database\Seeders;

use App\Support\AdminTenancySupport;
use Illuminate\Database\Seeder;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\User\App\Enums\PermissionEnum;

class RoleSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $guard = GuardEnum::WEB->value;
        $permissions = AdminTenancySupport::currentTenantPermissions([
            PermissionEnum::USER_VIEW->value,
            PermissionEnum::USER_CREATE->value,
            PermissionEnum::USER_UPDATE->value,
            PermissionEnum::USER_DELETE->value,
            PermissionEnum::USER_RESTORE->value,
            PermissionEnum::USER_IMPORT->value,
            PermissionEnum::USER_EXPORT->value,
        ], $guard);

        AdminTenancySupport::currentTenantRole(RoleEnum::SUPERADMIN->value, $guard)?->givePermissionTo($permissions);
        AdminTenancySupport::currentTenantRole(RoleEnum::ADMIN->value, $guard)?->givePermissionTo($permissions);
    }
}
