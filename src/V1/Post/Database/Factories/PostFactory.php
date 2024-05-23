<?php

namespace Src\V1\Post\Database\Factories;

use App\Models\User As UserModel;
use Src\V1\Post\Models\PostModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = PostModel::class;

    /**
     * @return array
     */
    public function definition()
    {
        return [

            "user_id" => UserModel::all()->random()->id,
            "content" => fake()->paragraph(),
        ];
    }
};
