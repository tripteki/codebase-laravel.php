<?php

namespace Database\Seeders\Stage;

use Illuminate\Database\Seeder;

class StageSeeder extends Seeder
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
