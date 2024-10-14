<?php

namespace Src\V1\Sample\Database\Seeders;

use Src\V1\Sample\Models\SampleModel;
use Illuminate\Database\Seeder;

class SampleSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run()
    {
        SampleModel::factory()->count(10)->create();
    }
};
