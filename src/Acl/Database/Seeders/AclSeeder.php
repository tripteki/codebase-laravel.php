<?php

namespace Modules\Acl\Database\Seeders;

use Illuminate\Database\Seeder;

class AclSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        sync_permissions_team_context();

        $this->call([

            PermissionSeeder::class,
            RoleSeeder::class,
        ]);
    }
}
