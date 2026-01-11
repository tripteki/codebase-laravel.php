<?php

namespace Src\V1\Api\Acl\Database\Seeders;

use Illuminate\Database\Seeder;

class AclSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $this->call([

            PermissionSeeder::class,
            RoleSeeder::class,
        ]);
    }
}
