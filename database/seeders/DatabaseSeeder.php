<?php

namespace Database\Seeders;

use Src\V1\Api\User\Database\Seeders\UserSeeder;
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

            UserSeeder::class,
        ]);
    }
}
