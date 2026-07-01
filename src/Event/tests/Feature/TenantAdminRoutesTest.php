<?php

namespace Modules\Event\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Modules\Acl\Database\Seeders\AclSeeder;
use Modules\Event\Database\Seeders\EventSeeder;
use Modules\User\Database\Seeders\CreateUserSeeder;
use Modules\User\Database\Seeders\UserSeeder;
use Tests\TestCase;

class TenantAdminRoutesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    protected User $tenantAdmin;

    /**
     * @var string
     */
    protected string $tenantPassword = "Password123!";

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutAdminApiMiddleware();
        $this->enablePermissionTeams();

        $this->artisan("db:seed", ["--class" => AclSeeder::class]);
        $this->artisan("db:seed", ["--class" => EventSeeder::class]);
        $this->artisan("db:seed", ["--class" => UserSeeder::class]);

        $this->tenantId = "scoped-event";

        $this->createTenantEvent($this->tenantId, [
            "title" => "Scoped Event",
        ]);

        $this->tenantAdmin = User::query()->where(
            "email",
            "admin.".$this->tenantId."@".config("app.email_server"),
        )->firstOrFail();
    }

    /**
     * @return void
     */
    public function test_get_tenant_admin_users_returns_404_for_invalid_tenant(): void
    {
        $token = $this->loginToken($this->resolveCentralAdmin(), CreateUserSeeder::DEFAULT_PASSWORD);

        $this->authGet("/api/v1/missing-event/admin/users", $token)
            ->assertStatus(404);
    }

    /**
     * @return void
     */
    public function test_get_admin_users_scopes_tenant_separately_from_central(): void
    {
        User::factory()->create([
            "name" => "Central User",
            "email" => "central-only@example.com",
            "password" => Hash::make($this->tenantPassword),
            "email_verified_at" => now(),
            "tenant_id" => null,
        ]);

        User::factory()->create([
            "name" => "Event User",
            "email" => "event-only@example.com",
            "password" => Hash::make($this->tenantPassword),
            "email_verified_at" => now(),
            "tenant_id" => $this->tenantId,
        ]);

        $centralToken = $this->loginToken($this->resolveCentralAdmin(), CreateUserSeeder::DEFAULT_PASSWORD);

        $this->authGet(
            $this->centralAdminApi("/users?filters=email:central-only"),
            $centralToken,
        )
            ->assertStatus(200)
            ->assertJsonFragment(["email" => "central-only@example.com"]);

        $tenantToken = $this->loginToken($this->tenantAdmin, CreateUserSeeder::DEFAULT_PASSWORD);

        $this->authGet(
            $this->adminApi("/users?filters=email:event-only"),
            $tenantToken,
        )
            ->assertStatus(200)
            ->assertJsonFragment(["email" => "event-only@example.com"])
            ->assertJsonMissing(["email" => "central-only@example.com"]);
    }

    /**
     * @return void
     */
    public function test_get_tenant_settings_variables(): void
    {
        $response = $this->getJson("/api/v1/{$this->tenantId}/settings/variables");

        $response->assertStatus(200)
            ->assertJsonStructure(["data" => ["COLOR_PRIMARY", "COLOR_SECONDARY", "COLOR_TERTIARY"]]);
    }

    /**
     * @return void
     */
    public function test_tenant_admin_notifications_returns_403_when_module_disabled(): void
    {
        $this->createTenantEvent("no-notification-module", [
            "title" => "No Notification Module",
            "add_ons_modules" => [\Modules\Event\App\Enums\AddOnEnum::MODULES_USER->value],
        ]);

        $tenantAdmin = User::query()->where(
            "email",
            "admin.no-notification-module@".config("app.email_server"),
        )->firstOrFail();

        $token = $this->loginToken($tenantAdmin, CreateUserSeeder::DEFAULT_PASSWORD);

        $this->tenantId = "no-notification-module";

        $this->authGet($this->adminApi("/notifications"), $token)
            ->assertStatus(403);
    }
}
