<?php

namespace Src\V1\Sample\Database\Seeders;

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
            SampleSeeder::class,
        ]);
    }
};
