<?php

namespace Src\V1\Api\User\Database\Seeders;

use Src\V1\Api\User\Enums\PermissionEnum;
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
        $guard = GuardEnum::WEB->value;

        foreach (PermissionEnum::cases() as $permission) {

            Permission::firstOrCreate([

                "name" => $permission->value,
                "guard_name" => $guard,
            ]);
        }
    }
}
