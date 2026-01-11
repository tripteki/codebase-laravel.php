<?php

namespace Src\V1\Api\Log\Database\Seeders;

use Src\V1\Api\Log\Enums\PermissionEnum;
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
        $permissions = [

            PermissionEnum::ACTIVITY_VIEW->value,
            PermissionEnum::ACTIVITY_DELETE->value,
        ];

        foreach ($permissions as $permission) {

            Permission::firstOrCreate([

                "name" => $permission,
                "guard_name" => GuardEnum::WEB->value,
            ]);

            Permission::firstOrCreate([

                "name" => $permission,
                "guard_name" => GuardEnum::API->value,
            ]);
        }
    }
}
