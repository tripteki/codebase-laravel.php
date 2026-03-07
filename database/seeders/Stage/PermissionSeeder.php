<?php

namespace Database\Seeders\Stage;

use App\Enum\Stage\StageMeetingPermissionEnum;
use App\Enum\Stage\StageSessionPermissionEnum;
use Illuminate\Database\Seeder;
use Src\V1\Api\Acl\Enums\GuardEnum;
use Src\V1\Api\Acl\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $guard = GuardEnum::WEB->value;

        foreach (StageMeetingPermissionEnum::cases() as $permission) {

            Permission::firstOrCreate([

                "name" => $permission->value,
                "guard_name" => $guard,
            ]);
        }

        foreach (StageSessionPermissionEnum::cases() as $permission) {

            Permission::firstOrCreate([

                "name" => $permission->value,
                "guard_name" => $guard,
            ]);
        }
    }
}
