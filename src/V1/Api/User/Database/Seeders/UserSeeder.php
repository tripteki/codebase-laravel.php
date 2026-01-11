<?php

namespace Src\V1\Api\User\Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $this->call([

            PermissionSeeder::class,
            RoleSeeder::class,
            CreateUserSeeder::class,
        ]);
    }
}
