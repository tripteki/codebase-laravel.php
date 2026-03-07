<?php

namespace Modules\User\Database\Seeders;

use Modules\User\App\Enums\PermissionEnum;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Models\Permission;
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
