<?php

namespace Modules\Event\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Event\Database\Seeders\PermissionSeeder as EventPermissionSeeder;
use Modules\Event\Database\Seeders\RoleSeeder as EventRoleSeeder;

class EventSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $this->call([
            EventPermissionSeeder::class,
            EventRoleSeeder::class,
        ]);
    }
}
