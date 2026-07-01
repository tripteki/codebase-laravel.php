<?php

namespace Modules\Acl\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\PermissionEnum;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Acl\App\Models\Permission;
use Modules\Acl\App\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $guard = GuardEnum::WEB->value;

        foreach (PermissionEnum::cases() as $permission) {
            Permission::firstOrCreate([

                "name" => $permission->value,
                "guard_name" => $guard,
                "tenant_id" => current_tenant_id(),
            ]);
        }

        $tenantId = current_tenant_id();

        if ($tenantId === null) {
            return;
        }

        $admin = Role::query()
            ->where("name", RoleEnum::ADMIN->value)
            ->where("guard_name", $guard)
            ->where("tenant_id", $tenantId)
            ->first();

        $admin?->givePermissionTo(Permission::query()
            ->where("guard_name", $guard)
            ->where("tenant_id", $tenantId)
            ->whereIn("name", [
                PermissionEnum::ROLE_VIEW->value,
                PermissionEnum::ROLE_IMPORT->value,
                PermissionEnum::PERMISSION_VIEW->value,
                PermissionEnum::PERMISSION_IMPORT->value,
            ])
            ->get());
    }
}
