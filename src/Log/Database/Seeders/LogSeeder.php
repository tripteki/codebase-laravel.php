<?php

namespace Modules\Log\Database\Seeders;

use Modules\Log\Database\Seeders\PermissionSeeder as LogPermissionSeeder;
use Modules\Log\Database\Seeders\RoleSeeder as LogRoleSeeder;
use Illuminate\Database\Seeder;

class LogSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $this->call([

            LogPermissionSeeder::class,
            LogRoleSeeder::class,
        ]);
    }
}
