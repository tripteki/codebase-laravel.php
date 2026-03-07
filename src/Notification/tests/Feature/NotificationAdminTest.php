<?php

namespace Modules\Notification\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Acl\Database\Seeders\AclSeeder;
use Modules\Notification\Database\Seeders\NotificationSeeder;
use Modules\User\Database\Seeders\UserSeeder;
use Tests\TestCase;

class NotificationAdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \App\Models\User
     */
    protected User $user;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan("db:seed", [ "--class" => AclSeeder::class, ]);
        $this->artisan("db:seed", [ "--class" => UserSeeder::class, ]);
        $this->artisan("db:seed", [ "--class" => NotificationSeeder::class, ]);

        $this->user = User::factory()->create([
            "password" => Hash::make("Password123!"),
            "email_verified_at" => now(),
        ]);

        $this->user->assignRole(RoleEnum::ADMIN->value);

        $this->actingAsJwt($this->user, "Password123!");

        $this->user->notifications()->create([
            "id" => \Illuminate\Support\Str::uuid()->toString(),
            "type" => \Illuminate\Notifications\Notification::class,
            "data" => [ "message" => "test", ],
        ]);
    }

    /**
     * @return void
     */
    public function test_admin_notifications_index(): void
    {
        $this->getJson("/api/v1/admin/notifications")->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_admin_notifications_show(): void
    {
        $notification = $this->user->notifications()->first();

        $this->getJson("/api/v1/admin/notifications/".$notification->id)->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_admin_notifications_deactivate_and_activate(): void
    {
        $notification = $this->user->notifications()->first();

        $this->deleteJson("/api/v1/admin/notifications/deactivate/".$notification->id)->assertStatus(200);
        $this->deleteJson("/api/v1/admin/notifications/activate/".$notification->id)->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_admin_notifications_forbidden_without_permission(): void
    {
        $restricted = User::factory()->create([
            "password" => Hash::make("Password123!"),
            "email_verified_at" => now(),
        ]);

        $this->flushHeaders();

        $login = $this->postJson("/api/v1/auth/login", [
            "identifierKey" => "email",
            "identifierValue" => $restricted->email,
            "password" => "Password123!",
        ]);

        $login->assertStatus(201);

        $this->withHeader("Authorization", "Bearer ".$login->json("accessToken"))
            ->getJson("/api/v1/admin/notifications")
            ->assertStatus(403);
    }
}
