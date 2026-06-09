<?php

namespace Modules\User\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Modules\Acl\Database\Seeders\AclSeeder;
use Modules\User\Database\Seeders\UserSeeder;
use Tests\TestCase;

class WebPushSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \App\Models\User
     */
    protected User $user;

    /**
     * @var string
     */
    protected string $password = "Password123!";

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan("db:seed", [ "--class" => AclSeeder::class, ]);
        $this->artisan("db:seed", [ "--class" => UserSeeder::class, ]);

        $this->user = User::factory()->create([
            "password" => Hash::make($this->password),
            "email_verified_at" => now(),
        ]);
    }

    /**
     * @return string
     */
    protected function accessToken(): string
    {
        $login = $this->postJson("/api/v1/auth/login", [
            "identifierKey" => "email",
            "identifierValue" => $this->user->email,
            "password" => $this->password,
        ]);

        $login->assertStatus(201);

        return (string) $login->json("accessToken");
    }

    /**
     * @return void
     */
    public function test_post_v1_webpush_subscribe(): void
    {
        $token = $this->accessToken();

        $response = $this->withHeader("Authorization", "Bearer {$token}")
            ->postJson("/api/v1/webpush/subscribe", [
                "endpoint" => "https://push.example.test/subscription",
                "keys" => [
                    "p256dh" => "test-p256dh",
                    "auth" => "test-auth",
                ],
            ]);

        $response->assertStatus(200)
            ->assertJsonPath("success", true);

        $this->assertDatabaseHas("push_subscriptions", [
            "subscribable_id" => $this->user->getKey(),
            "subscribable_type" => User::class,
            "endpoint" => "https://push.example.test/subscription",
        ]);
    }
}
