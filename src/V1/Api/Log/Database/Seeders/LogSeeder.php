<?php

namespace Src\V1\Api\Log\Database\Seeders;

use Src\V1\Api\Log\Database\Seeders\PermissionSeeder as LogPermissionSeeder;
use Src\V1\Api\Log\Database\Seeders\RoleSeeder as LogRoleSeeder;
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
