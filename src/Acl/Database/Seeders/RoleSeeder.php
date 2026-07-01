<?php

namespace Modules\Acl\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\PermissionEnum;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Acl\App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $guard = GuardEnum::WEB->value;

        $tenantId = current_tenant_id();
        $createRole = static fn (RoleEnum $role) => Role::firstOrCreate([
            "name" => $role->value,
            "guard_name" => $guard,
            "tenant_id" => $tenantId,
        ]);

        $superadminPermissions = [
            PermissionEnum::ROLE_VIEW->value,
            PermissionEnum::ROLE_CREATE->value,
            PermissionEnum::ROLE_UPDATE->value,
            PermissionEnum::ROLE_DELETE->value,
            PermissionEnum::ROLE_IMPORT->value,
            PermissionEnum::ROLE_EXPORT->value,
            PermissionEnum::PERMISSION_VIEW->value,
            PermissionEnum::PERMISSION_CREATE->value,
            PermissionEnum::PERMISSION_UPDATE->value,
            PermissionEnum::PERMISSION_DELETE->value,
            PermissionEnum::PERMISSION_IMPORT->value,
            PermissionEnum::PERMISSION_EXPORT->value,
        ];

        if ($tenantId === null) {
            $superadmin = $createRole(RoleEnum::SUPERADMIN);
            $superadmin?->givePermissionTo($superadminPermissions);

            $createRole(RoleEnum::ADMIN);

            foreach (RoleEnum::tenantBootstrapRoles() as $role) {
                if ($role === RoleEnum::ADMIN) {
                    continue;
                }

                $createRole($role);
            }
        } else {
            Role::query()
                ->where("name", RoleEnum::SUPERADMIN->value)
                ->where("guard_name", $guard)
                ->where("tenant_id", $tenantId)
                ->delete();

            foreach (RoleEnum::tenantBootstrapRoles() as $role) {
                $createRole($role);
            }
        }
    }
}
