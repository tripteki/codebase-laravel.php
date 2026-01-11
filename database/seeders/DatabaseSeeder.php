<?php

namespace Database\Seeders;

use Src\V1\Api\Acl\Database\Seeders\AclSeeder;
use Src\V1\Api\User\Database\Seeders\UserSeeder;
use Src\V1\Api\Log\Database\Seeders\LogSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([

            AclSeeder::class,
            UserSeeder::class,
            LogSeeder::class,
        ]);
    }
}
