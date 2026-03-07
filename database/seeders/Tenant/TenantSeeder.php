<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        if (! config("tenancy.is_tenancy")) {
            return;
        }

        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);
    }
}
