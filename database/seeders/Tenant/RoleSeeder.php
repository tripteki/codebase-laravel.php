<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        if (! config("tenancy.is_tenancy")) {
            return;
        }

        //
    }
}
