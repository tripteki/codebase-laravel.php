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

        $aclPermissions = array_map(
            fn (PermissionEnum $permission) => $permission->value,
            PermissionEnum::cases(),
        );

        if ($tenantId === null) {
            $superadmin = $createRole(RoleEnum::SUPERADMIN);
            $superadmin?->givePermissionTo(array_map(
                fn (PermissionEnum $permission) => $permission->value,
                PermissionEnum::cases(),
            ));

            $admin = $createRole(RoleEnum::ADMIN);
            $admin?->givePermissionTo($aclPermissions);

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
                $created = $createRole($role);

                if ($role === RoleEnum::ADMIN) {
                    $created?->givePermissionTo($aclPermissions);
                }
            }
        }
    }
}
