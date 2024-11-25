<?php

namespace Src\V1\Api\User\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = User::class;

    /**
     * @var string|null
     */
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            "name" => fake()->name(),
            "email" => fake()->unique()->safeEmail(),
            "email_verified_at" => now(),
            "password" => static::$password ??= Hash::make("password"),
            "remember_token" => Str::random(10),
        ];
    }

    /**
     * @return static
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [

            "email_verified_at" => null,
        ]);
    }
}
