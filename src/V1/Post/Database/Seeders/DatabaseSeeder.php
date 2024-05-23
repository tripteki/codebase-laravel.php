<?php

namespace Src\V1\Post\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run()
    {
        $this->call(
        [
            PostSeeder::class,
        ]);
    }
};
