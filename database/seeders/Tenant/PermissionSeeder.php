<?php

namespace Database\Seeders\Tenant;

use App\Enum\Tenant\PermissionEnum;
use Src\V1\Api\Acl\Enums\GuardEnum;
use Src\V1\Api\Acl\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        if (! config("tenancy.is_tenancy")) {
            return;
        }

        $guard = GuardEnum::WEB->value;

        foreach (PermissionEnum::cases() as $permission) {

            Permission::firstOrCreate([

                "name" => $permission->value,
                "guard_name" => $guard,
            ]);
        }
    }
}
