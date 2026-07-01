<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Acl\App\Models\Permission;
use Modules\Acl\App\Models\Role;
use Modules\Acl\Database\Seeders\AclSeeder;
use Modules\User\Database\Seeders\CreateUserSeeder;
use Modules\User\Database\Seeders\UserSeeder;
use Tests\TestCase;

class AdminSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    protected User $centralAdmin;

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
        $this->artisan("db:seed", ["--class" => UserSeeder::class]);

        $this->centralAdmin = $this->resolveCentralAdmin();

        $this->tenantId = "search-tenant";

        $this->createTenantEvent($this->tenantId, ["title" => "Search Tenant"]);

        $this->tenantAdmin = User::factory()->create([
            "name" => "Tenant Search Admin",
            "email" => "tenant-search-admin@example.com",
            "password" => Hash::make($this->tenantPassword),
            "email_verified_at" => now(),
            "tenant_id" => $this->tenantId,
        ]);

        $this->assignTenantRole($this->tenantAdmin, RoleEnum::ADMIN->value, $this->tenantId);
    }

    /**
     * @return void
     */
    public function test_get_admin_search_users(): void
    {
        User::factory()->create([
            "name" => "Central Search Target",
            "email" => "central-target@example.com",
            "email_verified_at" => now(),
        ]);

        $this->authGet(
            $this->centralAdminApi("/search?q=Central+Search+Target&category=users"),
            $this->loginToken($this->centralAdmin, CreateUserSeeder::DEFAULT_PASSWORD),
        )
            ->assertStatus(200)
            ->assertJsonPath("results.0.category", "users")
            ->assertJsonFragment(["title" => "Central Search Target"]);

        User::factory()->create([
            "name" => "Tenant Search Target",
            "email" => "tenant-target@example.com",
            "email_verified_at" => now(),
            "tenant_id" => $this->tenantId,
        ]);

        $this->authGet(
            $this->adminApi("/search?q=Tenant+Search+Target&category=users"),
            $this->loginToken($this->tenantAdmin, $this->tenantPassword),
        )
            ->assertStatus(200)
            ->assertJsonPath("results.0.category", "users")
            ->assertJsonFragment(["title" => "Tenant Search Target"]);
    }

    /**
     * @return void
     */
    public function test_get_admin_search_roles(): void
    {
        Role::create([
            "name" => "central-search-role",
            "guard_name" => GuardEnum::WEB->value,
        ]);

        $this->authGet(
            $this->centralAdminApi("/search?q=central-search-role&category=roles"),
            $this->loginToken($this->centralAdmin, CreateUserSeeder::DEFAULT_PASSWORD),
        )
            ->assertStatus(200)
            ->assertJsonPath("results.0.category", "roles")
            ->assertJsonFragment(["title" => "central-search-role"]);

        Role::create([
            "name" => "tenant-search-role",
            "guard_name" => GuardEnum::WEB->value,
            "tenant_id" => $this->tenantId,
        ]);

        $this->authGet(
            $this->adminApi("/search?q=tenant-search-role&category=roles"),
            $this->loginToken($this->tenantAdmin, $this->tenantPassword),
        )
            ->assertStatus(200)
            ->assertJsonPath("results.0.category", "roles")
            ->assertJsonFragment(["title" => "tenant-search-role"]);
    }

    /**
     * @return void
     */
    public function test_get_admin_search_permissions(): void
    {
        Permission::create([
            "name" => "central.searchable",
            "guard_name" => GuardEnum::WEB->value,
        ]);

        $this->authGet(
            $this->centralAdminApi("/search?q=central.searchable&category=permissions"),
            $this->loginToken($this->centralAdmin, CreateUserSeeder::DEFAULT_PASSWORD),
        )
            ->assertStatus(200)
            ->assertJsonPath("results.0.category", "permissions")
            ->assertJsonFragment(["title" => "central.searchable"]);

        Permission::create([
            "name" => "tenant.searchable",
            "guard_name" => GuardEnum::WEB->value,
            "tenant_id" => $this->tenantId,
        ]);

        $this->authGet(
            $this->adminApi("/search?q=tenant.searchable&category=permissions"),
            $this->loginToken($this->tenantAdmin, $this->tenantPassword),
        )
            ->assertStatus(200)
            ->assertJsonPath("results.0.category", "permissions")
            ->assertJsonFragment(["title" => "tenant.searchable"]);
    }

    /**
     * @return void
     */
    public function test_get_admin_search_events(): void
    {
        $this->authGet(
            $this->centralAdminApi("/search?q=Search&category=events"),
            $this->loginToken($this->centralAdmin, CreateUserSeeder::DEFAULT_PASSWORD),
        )->assertStatus(200);

        $this->authGet(
            $this->adminApi("/search?q=Search&category=events"),
            $this->loginToken($this->tenantAdmin, $this->tenantPassword),
        )
            ->assertStatus(200)
            ->assertJsonPath("results", []);
    }
}
