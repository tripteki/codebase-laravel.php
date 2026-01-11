<?php

namespace Src\V1\Api\User\Database\Seeders;

use Src\V1\Api\Acl\Models\Role;
use Src\V1\Api\Acl\Enums\RoleEnum;
use Src\V1\Api\User\Enums\PermissionEnum;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $superAdmin = Role::where("name", RoleEnum::SUPERADMIN->value)->first();
        $superAdmin?->givePermissionTo(array_column(PermissionEnum::cases(), "value"));

        $admin = Role::where("name", RoleEnum::ADMIN->value)->first();
        $admin?->givePermissionTo([

            PermissionEnum::USER_VIEW->value,
            PermissionEnum::USER_CREATE->value,
            PermissionEnum::USER_UPDATE->value,
            PermissionEnum::USER_DELETE->value,
            PermissionEnum::USER_RESTORE->value,
            PermissionEnum::USER_IMPORT_VIEW->value,
            PermissionEnum::USER_IMPORT_CREATE->value,
            PermissionEnum::USER_IMPORT_UPLOAD->value,
            PermissionEnum::USER_EXPORT_VIEW->value,
            PermissionEnum::USER_EXPORT_CREATE->value,
            PermissionEnum::USER_EXPORT_DOWNLOAD->value,
        ]);

        $user = Role::where("name", RoleEnum::USER->value)->first();
        $user?->givePermissionTo([

            PermissionEnum::USER_VIEW->value,
            PermissionEnum::USER_CREATE->value,
            PermissionEnum::USER_UPDATE->value,
            PermissionEnum::USER_EXPORT_VIEW->value,
            PermissionEnum::USER_EXPORT_CREATE->value,
            PermissionEnum::USER_EXPORT_DOWNLOAD->value,
        ]);

        $guest = Role::where("name", RoleEnum::GUEST->value)->first();
        $guest?->givePermissionTo([

            PermissionEnum::USER_VIEW->value,
        ]);
    }
}
