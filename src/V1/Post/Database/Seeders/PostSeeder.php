<?php

namespace Src\V1\Post\Database\Seeders;

use Src\V1\Post\Models\PostModel;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run()
    {
        PostModel::factory()->count(10)->create();
    }
};
