<?php

namespace Modules\Event\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Acl\App\Models\Role;
use Modules\Event\App\Enums\PermissionEnum;

class RoleSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $admin = Role::query()->firstOrCreate([
            "name" => RoleEnum::ADMIN->value,
            "guard_name" => GuardEnum::WEB->value,
            "tenant_id" => current_tenant_id(),
        ]);

        $admin?->givePermissionTo(array_map(
            static fn (PermissionEnum $permission) => $permission->value,
            PermissionEnum::cases(),
        ));
    }
}
