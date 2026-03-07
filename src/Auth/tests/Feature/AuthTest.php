<?php

namespace Modules\Auth\Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

        $this->artisan('db:seed', ['--class' => \Modules\Acl\Database\Seeders\AclSeeder::class]);
        $this->artisan('db:seed', ['--class' => \Modules\User\Database\Seeders\UserSeeder::class]);
        $this->artisan('db:seed', ['--class' => \Modules\Log\Database\Seeders\LogSeeder::class]);

        $this->user = User::factory()->create([
            "password" => Hash::make($this->password),
            "email_verified_at" => now(),
        ]);
    }

    /**
     * @return array{accessToken: string, refreshToken: string}
     */
    protected function loginTokens(): array
    {
        $login = $this->postJson("/api/v1/auth/login", [
            "identifierKey" => "email",
            "identifierValue" => $this->user->email,
            "password" => $this->password,
        ]);

        $login->assertStatus(201)
            ->assertJsonStructure([
                "accessToken",
                "refreshToken",
                "accessTokenTtl",
                "refreshTokenTtl",
            ]);

        return [
            "accessToken" => $login->json("accessToken"),
            "refreshToken" => $login->json("refreshToken"),
        ];
    }

    /**
     * @return void
     */
    public function test_auth_register_validation_detail(): void
    {
        $test = $this->postJson("/api/v1/auth/register", [
            "name" => "A",
            "email" => "invalid-email",
        ]);

        $test->assertStatus(422)
            ->assertJsonStructure([
                "detail" => [
                    "*" => [ "type", "loc", "msg", "input", "ctx", ],
                ],
            ]);
    }

    /**
     * @return void
     */
    public function test_auth_login_invalid_credentials(): void
    {
        $test = $this->postJson("/api/v1/auth/login", [
            "identifierKey" => "email",
            "identifierValue" => $this->user->email,
            "password" => "WrongPassword!",
        ]);

        $test->assertStatus(401)
            ->assertJsonPath("detail", __("auth.failed"));
    }

    /**
     * @return void
     */
    public function test_auth_register(): void
    {
        Notification::fake();

        $test = $this->postJson("/api/v1/auth/register", [
            "name" => "Test User",
            "email" => "testuser@example.com",
            "password" => $this->password,
            "password_confirmation" => $this->password,
        ]);

        $test->assertStatus(201)
            ->assertJsonStructure([
                "id",
                "name",
                "email",
                "email_verified_at",
                "created_at",
                "updated_at",
            ]);
    }

    /**
     * @return void
     */
    public function test_auth_login(): void
    {
        $this->loginTokens();
    }

    /**
     * @return void
     */
    public function test_auth_refresh(): void
    {
        $tokens = $this->loginTokens();

        $test = $this->withHeader("Authorization", "Bearer {$tokens["refreshToken"]}")
            ->putJson("/api/v1/auth/refresh");

        $test->assertStatus(200)
            ->assertJsonStructure([
                "accessToken",
                "refreshToken",
                "accessTokenTtl",
                "refreshTokenTtl",
            ]);
    }

    /**
     * @return void
     */
    public function test_auth_logout(): void
    {
        $tokens = $this->loginTokens();

        $test = $this->withHeader("Authorization", "Bearer {$tokens["accessToken"]}")
            ->postJson("/api/v1/auth/logout");

        $test->assertStatus(200);
        $this->assertSame(true, $test->json());
    }

    /**
     * @return void
     */
    public function test_auth_email_verification_notification(): void
    {
        $tokens = $this->loginTokens();

        Notification::fake();

        $test = $this->withHeader("Authorization", "Bearer {$tokens["accessToken"]}")
            ->postJson("/api/v1/auth/email/verification-notification");

        $test->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_auth_forgot_password(): void
    {
        Mail::fake();

        $test = $this->postJson("/api/v1/auth/forgot-password", [
            "email" => $this->user->email,
        ]);

        $test->assertStatus(200);

        $unknown = $this->postJson("/api/v1/auth/forgot-password", [
            "email" => "unknown@example.com",
        ]);

        $unknown->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_auth_me_get(): void
    {
        $tokens = $this->loginTokens();

        $this->withHeader("Authorization", "Bearer {$tokens["accessToken"]}")
            ->getJson("/api/v1/auth/me")
            ->assertStatus(200)
            ->assertJsonStructure([
                "id",
                "name",
                "email",
                "email_verified_at",
                "created_at",
                "updated_at",
            ]);
    }

    /**
     * @return void
     */
    public function test_auth_verify_email_post(): void
    {
        $user = User::factory()->create([
            "email" => "verify-me@example.com",
            "email_verified_at" => null,
            "password" => Hash::make($this->password),
        ]);

        $signedUrl = signed_frontend_url("auth/verify-email/".$user->email);
        parse_str((string) parse_url($signedUrl, PHP_URL_QUERY), $query);

        $this->postJson("/api/v1/auth/verify-email/".$user->email."?signed=".urlencode((string) $query["signed"]))
            ->assertStatus(200)
            ->assertJsonPath("email", $user->email);

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    /**
     * @return void
     */
    public function test_auth_reset_password_signed_post(): void
    {
        $signedUrl = signed_frontend_url("auth/reset-password/".$this->user->email);
        parse_str((string) parse_url($signedUrl, PHP_URL_QUERY), $query);
        $signed = (string) $query["signed"];

        DB::table("password_reset_tokens")->updateOrInsert(
            [ "email" => $this->user->email, ],
            [ "token" => $signed, "created_at" => now(), ]
        );

        $this->postJson("/api/v1/auth/reset-password/".$this->user->email."?signed=".urlencode($signed), [
            "password" => "NewPassword123!",
            "password_confirmation" => "NewPassword123!",
        ])->assertStatus(200)
            ->assertJsonPath("email", $this->user->email);
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

        $test->assertStatus(200)
            ->assertJsonStructure([
                "id",
                "name",
                "email",
                "email_verified_at",
            ]);
    }
}
