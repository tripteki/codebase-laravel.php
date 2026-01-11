<?php

namespace Src\V1\Api\Acl\Database\Seeders;

use Src\V1\Api\Acl\Models\Role;
use Src\V1\Api\Acl\Enums\RoleEnum;
use Src\V1\Api\Acl\Enums\PermissionEnum;
use Src\V1\Api\Acl\Enums\GuardEnum;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $superAdmin = Role::firstOrCreate([

            "name" => RoleEnum::SUPERADMIN->value,
            "guard_name" => GuardEnum::WEB->value,
        ]);

        $superAdmin->givePermissionTo(array_column(PermissionEnum::cases(), "value"));

        $admin = Role::firstOrCreate([

            "name" => RoleEnum::ADMIN->value,
            "guard_name" => GuardEnum::WEB->value,
        ]);

        $admin->givePermissionTo([

            PermissionEnum::ROLE_VIEW->value,
            PermissionEnum::ROLE_CREATE->value,
            PermissionEnum::ROLE_UPDATE->value,
            PermissionEnum::ROLE_DELETE->value,
            PermissionEnum::ROLE_IMPORT_VIEW->value,
            PermissionEnum::ROLE_IMPORT_CREATE->value,
            PermissionEnum::ROLE_IMPORT_UPLOAD->value,
            PermissionEnum::ROLE_IMPORT_DELETE->value,
            PermissionEnum::ROLE_EXPORT_VIEW->value,
            PermissionEnum::ROLE_EXPORT_CREATE->value,
            PermissionEnum::ROLE_EXPORT_DOWNLOAD->value,
            PermissionEnum::ROLE_EXPORT_DELETE->value,

            PermissionEnum::PERMISSION_VIEW->value,
            PermissionEnum::PERMISSION_CREATE->value,
            PermissionEnum::PERMISSION_UPDATE->value,
            PermissionEnum::PERMISSION_DELETE->value,
            PermissionEnum::PERMISSION_IMPORT_VIEW->value,
            PermissionEnum::PERMISSION_IMPORT_CREATE->value,
            PermissionEnum::PERMISSION_IMPORT_UPLOAD->value,
            PermissionEnum::PERMISSION_IMPORT_DELETE->value,
            PermissionEnum::PERMISSION_EXPORT_VIEW->value,
            PermissionEnum::PERMISSION_EXPORT_CREATE->value,
            PermissionEnum::PERMISSION_EXPORT_DOWNLOAD->value,
            PermissionEnum::PERMISSION_EXPORT_DELETE->value,
        ]);

        Role::firstOrCreate([

            "name" => RoleEnum::USER->value,
            "guard_name" => GuardEnum::WEB->value,
        ]);

        Role::firstOrCreate([

            "name" => RoleEnum::GUEST->value,
            "guard_name" => GuardEnum::WEB->value,
        ]);
    }
}
