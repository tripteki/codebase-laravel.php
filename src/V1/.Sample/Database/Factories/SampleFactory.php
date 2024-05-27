<?php

namespace Src\V1\Sample\Database\Factories;

use App\Models\User As UserModel;
use Src\V1\Sample\Models\SampleModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class SampleFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = SampleModel::class;

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
