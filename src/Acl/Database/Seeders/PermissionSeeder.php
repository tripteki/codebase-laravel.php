<?php

namespace Modules\Acl\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\PermissionEnum;
use Modules\Acl\App\Models\Permission;

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
    }
}
