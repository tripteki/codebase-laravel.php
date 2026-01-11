<?php

namespace Src\V1\Api\User\Database\Seeders;

use Src\V1\Api\Acl\Models\Permission;
use Src\V1\Api\User\Enums\PermissionEnum;
use Src\V1\Api\Acl\Enums\GuardEnum;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $permissions = array_column(PermissionEnum::cases(), "value");

        foreach ($permissions as $permission) {

            Permission::firstOrCreate([

                "name" => $permission,
                "guard_name" => GuardEnum::WEB->value,
            ]);
        }
    }
}
