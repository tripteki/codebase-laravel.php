<?php

namespace Tests\Feature\V1\Api\Auth;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \App\Models\User
     */
    protected $user;

    /**
     * @var string
     */
    protected $password = "Password123!";

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => \Src\V1\Api\Acl\Database\Seeders\AclSeeder::class]);
        $this->artisan('db:seed', ['--class' => \Src\V1\Api\User\Database\Seeders\UserSeeder::class]);
        $this->artisan('db:seed', ['--class' => \Src\V1\Api\Log\Database\Seeders\LogSeeder::class]);

        $this->user = User::factory()->create([
            "password" => Hash::make($this->password),
            "email_verified_at" => now(),
        ]);
    }

    /**
     * @return void
     */
    public function test_auth_register(): void
    {
        $test = $this->postJson("/api/v1/auth/register", [
            "name" => "Test User",
            "email" => "testuser@example.com",
            "password" => $this->password,
            "password_confirmation" => $this->password,
        ]);

        $test->assertStatus(201);
    }

    /**
     * @return void
     */
    public function test_auth_login(): void
    {
        $test = $this->postJson("/api/v1/auth/login", [
            "identifier" => $this->user->email,
            "password" => $this->password,
        ]);

        $test->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_auth_refresh(): void
    {
        $login = $this->postJson("/api/v1/auth/login", [
            "identifier" => $this->user->email,
            "password" => $this->password,
        ]);

        $token = $login->json("token");

        $test = $this->withHeader("Authorization", "Bearer {$token}")
            ->putJson("/api/v1/auth/refresh");

        $test->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_auth_logout(): void
    {
        $login = $this->postJson("/api/v1/auth/login", [
            "identifier" => $this->user->email,
            "password" => $this->password,
        ]);

        $token = $login->json("token");

        $test = $this->withHeader("Authorization", "Bearer {$token}")
            ->postJson("/api/v1/auth/logout");

        $test->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_auth_email_verification_notification(): void
    {
        $login = $this->postJson("/api/v1/auth/login", [
            "identifier" => $this->user->email,
            "password" => $this->password,
        ]);

        Notification::fake();

        $token = $login->json("token");

        $test = $this->withHeader("Authorization", "Bearer {$token}")
            ->postJson("/api/v1/auth/email/verification-notification");

        $test->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_auth_forgot_password(): void
    {
        Notification::fake();

        $test = $this->postJson("/api/v1/auth/forgot-password", [
            "email" => $this->user->email,
        ]);

        $test->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_auth_reset_password(): void
    {
        $token = Password::createToken($this->user);

        $test = $this->postJson("/api/v1/auth/reset-password", [
            "token" => $token,
            "email" => $this->user->email,
            "password" => "NewPassword123!",
            "password_confirmation" => "NewPassword123!",
        ]);

        $test->assertStatus(200);
    }
}
