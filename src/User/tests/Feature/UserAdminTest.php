<?php

namespace Modules\User\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Modules\Acl\Database\Seeders\AclSeeder;
use Modules\User\App\Jobs\UserAdminExportJob;
use Modules\User\App\Repositories\UserAdminRepository;
use Modules\User\Database\Seeders\CreateUserSeeder;
use Modules\User\Database\Seeders\UserSeeder;
use Tests\InteractsWithTenancy;
use Tests\TestCase;

class UserAdminTest extends TestCase
{
    use InteractsWithTenancy,
        RefreshDatabase;

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

        $this->withoutAdminApiMiddleware();
        $this->enablePermissionTeams();

        $this->artisan("db:seed", ["--class" => AclSeeder::class]);
        $this->artisan("db:seed", ["--class" => UserSeeder::class]);

        $this->user = $this->resolveCentralAdmin();

        $this->actingAsJwt($this->user, CreateUserSeeder::DEFAULT_PASSWORD);
    }

    /**
     * @return void
     */
    public function test_admin_users_index(): void
    {
        $this->getJson("/api/v1/admin/users")
            ->assertStatus(200)
            ->assertJsonStructure([
                "totalPage",
                "perPage",
                "currentPage",
                "nextPage",
                "previousPage",
                "firstPage",
                "lastPage",
                "data",
            ]);
    }

    /**
     * @return void
     */
    public function test_admin_users_search_filters_by_q(): void
    {
        User::factory()->create([
            "name" => "searchable-user",
            "email" => "searchable-user@example.com",
            "email_verified_at" => now(),
            "tenant_id" => null,
        ]);

        $this->getJson("/api/v1/admin/users?filters=q:searchable-user")
            ->assertStatus(200)
            ->assertJsonFragment([
                "email" => "searchable-user@example.com",
            ]);
    }

    /**
     * @return void
     */
    public function test_admin_users_filters_by_verified(): void
    {
        User::factory()->create([
            "name" => "verified-user",
            "email" => "verified-user@example.com",
            "email_verified_at" => now(),
            "tenant_id" => null,
        ]);

        User::factory()->unverified()->create([
            "name" => "unverified-user",
            "email" => "unverified-user@example.com",
            "tenant_id" => null,
        ]);

        $this->getJson("/api/v1/admin/users?filters=verified:yes")
            ->assertStatus(200)
            ->assertJsonFragment([
                "email" => "verified-user@example.com",
            ])
            ->assertJsonMissing([
                "email" => "unverified-user@example.com",
            ]);

        $this->getJson("/api/v1/admin/users?filters=verified:no")
            ->assertStatus(200)
            ->assertJsonFragment([
                "email" => "unverified-user@example.com",
            ])
            ->assertJsonMissing([
                "email" => "verified-user@example.com",
            ]);
    }

    /**
     * @return void
     */
    public function test_admin_users_filters_by_status(): void
    {
        $active = User::factory()->create([
            "name" => "active-user",
            "email" => "active-user@example.com",
            "email_verified_at" => now(),
            "tenant_id" => null,
        ]);

        $inactive = User::factory()->create([
            "name" => "inactive-user",
            "email" => "inactive-user@example.com",
            "email_verified_at" => now(),
            "tenant_id" => null,
        ]);
        $inactive->delete();

        $this->getJson("/api/v1/admin/users?filters=status:active")
            ->assertStatus(200)
            ->assertJsonFragment([
                "email" => "active-user@example.com",
            ])
            ->assertJsonMissing([
                "email" => "inactive-user@example.com",
            ]);

        $this->getJson("/api/v1/admin/users?filters=status:inactive")
            ->assertStatus(200)
            ->assertJsonFragment([
                "email" => "inactive-user@example.com",
            ])
            ->assertJsonMissing([
                "email" => "active-user@example.com",
            ]);
    }

    /**
     * @return void
     */
    public function test_central_admin_users_index_includes_tenant_users_without_tenant_filter(): void
    {
        $this->createTenantEvent("tenant-users-list", [
            "title" => "Tenant Users List",
        ]);

        User::factory()->create([
            "name" => "tenant-only-user",
            "email" => "tenant-only-user@example.com",
            "email_verified_at" => now(),
            "tenant_id" => "tenant-users-list",
        ]);

        $this->getJson("/api/v1/admin/users")
            ->assertOk()
            ->assertJsonFragment([
                "email" => "tenant-only-user@example.com",
            ])
            ->assertJsonFragment([
                "email" => $this->user->email,
            ]);
    }

    /**
     * @return void
     */
    public function test_central_admin_users_index_without_status_filter_excludes_inactive_users(): void
    {
        $inactive = User::factory()->create([
            "name" => "inactive-listed-user",
            "email" => "inactive-listed-user@example.com",
            "email_verified_at" => now(),
            "tenant_id" => null,
        ]);
        $inactive->delete();

        $this->getJson("/api/v1/admin/users")
            ->assertOk()
            ->assertJsonMissing([ "email" => "inactive-listed-user@example.com", ]);
    }

    /**
     * @return void
     */
    public function test_central_admin_users_index_with_status_all_includes_inactive_users(): void
    {
        $inactive = User::factory()->create([
            "name" => "inactive-all-status-user",
            "email" => "inactive-all-status-user@example.com",
            "email_verified_at" => now(),
            "tenant_id" => null,
        ]);
        $inactive->delete();

        $this->getJson("/api/v1/admin/users?filters=status:all")
            ->assertOk()
            ->assertJsonFragment([
                "email" => "inactive-all-status-user@example.com",
            ]);
    }

    /**
     * @return void
     */
    public function test_central_admin_users_index_with_status_active_excludes_inactive_users(): void
    {
        $inactive = User::factory()->create([
            "name" => "inactive-active-status-user",
            "email" => "inactive-active-status-user@example.com",
            "email_verified_at" => now(),
            "tenant_id" => null,
        ]);
        $inactive->delete();

        $this->getJson("/api/v1/admin/users?filters=status:active")
            ->assertOk()
            ->assertJsonMissing([ "email" => "inactive-active-status-user@example.com", ]);
    }

    /**
     * @return void
     */
    public function test_admin_users_store(): void
    {
        $this->postJson("/api/v1/admin/users", [
            "tenant" => "central",
            "name" => "Admin Created",
            "full_name" => "Admin Created Full Name",
            "email" => "admin-created@example.com",
            "password" => "Password123!",
            "password_confirmation" => "Password123!",
        ])->assertStatus(201)
            ->assertJsonPath("full_name", "Admin Created Full Name");
    }

    /**
     * @return void
     */
    public function test_admin_users_store_for_tenant_event(): void
    {
        $this->createTenantEvent("tenant-user-create", [
            "title" => "Tenant User Create",
        ]);

        $this->postJson("/api/v1/admin/users", [
            "tenant" => "tenant-user-create",
            "name" => "Tenant Created",
            "full_name" => "Tenant Created Full Name",
            "email" => "tenant-created@example.com",
            "password" => "Password123!",
            "password_confirmation" => "Password123!",
        ])->assertStatus(201)
            ->assertJsonPath("tenant_id", "tenant-user-create");
    }

    /**
     * @return void
     */
    public function test_admin_users_show(): void
    {
        $this->getJson("/api/v1/admin/users/".$this->user->id)->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_admin_users_deactivate_and_activate(): void
    {
        Mail::fake();

        $target = User::factory()->create([ "email_verified_at" => now(), ]);

        $this->deleteJson("/api/v1/admin/users/deactivate/".$target->id)->assertStatus(200);
        $this->deleteJson("/api/v1/admin/users/activate/".$target->id)->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_admin_users_force_delete(): void
    {
        Mail::fake();

        $target = User::factory()->create([ "email_verified_at" => now(), ]);

        $this->deleteJson("/api/v1/admin/users/deactivate/".$target->id)->assertStatus(200);
        $this->deleteJson("/api/v1/admin/users/force-delete/".$target->id)->assertStatus(200);

        $this->assertDatabaseMissing("users", [ "id" => $target->id, ]);
    }

    /**
     * @return void
     */
    public function test_admin_users_update(): void
    {
        $this->putJson("/api/v1/admin/users/".$this->user->id, [
            "name" => "Updated Name",
            "full_name" => "Updated Full Name",
        ])->assertStatus(200)
            ->assertJsonPath("name", "Updated Name")
            ->assertJsonPath("full_name", "Updated Full Name");
    }

    /**
     * @return void
     */
    public function test_admin_users_verify(): void
    {
        $target = User::factory()->create([ "email_verified_at" => null, ]);

        $this->putJson("/api/v1/admin/users/verify/".$target->id)
            ->assertStatus(200);

        $this->assertNotNull($target->fresh()->email_verified_at);
    }

    /**
     * @return void
     */
    public function test_admin_users_import(): void
    {
        $csv = "name,email,password\ntest-import,import-user@example.com,Password123!";

        $this->post("/api/v1/admin/users/import", [
            "file" => UploadedFile::fake()->createWithContent("users.csv", $csv),
        ])->assertStatus(200);

        $this->assertDatabaseHas("users", [ "email" => "import-user@example.com", ]);
        $this->assertDatabaseHas("profiles", [ "full_name" => "test-import", ]);
    }

    /**
     * @return void
     */
    public function test_central_admin_user_import_assigns_tenant_from_column(): void
    {
        $this->createTenantEvent("tenant-user-import", [
            "title" => "Tenant User Import",
        ]);

        $csv = "tenant,name,email,password\ntenant-user-import,tenant-import-user,tenant-import-user@example.com,Password123!";

        $this->post("/api/v1/admin/users/import", [
            "file" => UploadedFile::fake()->createWithContent("users.csv", $csv),
        ])->assertStatus(200);

        $this->assertDatabaseHas("users", [
            "email" => "tenant-import-user@example.com",
            "tenant_id" => "tenant-user-import",
        ]);
        $this->assertDatabaseHas("profiles", [
            "full_name" => "tenant-import-user",
        ]);
    }

    /**
     * @return void
     */
    public function test_admin_users_export(): void
    {
        $this->post("/api/v1/admin/users/export?type=csv")
            ->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_admin_users_export_to_file_respects_filters(): void
    {
        Queue::fake();

        $this->post(
            "/api/v1/admin/users/export?type=csv&filters=verified:yes,q:export-verified",
        )->assertStatus(200);

        Queue::assertPushed(UserAdminExportJob::class, fn (UserAdminExportJob $job): bool => ($job->filters["verified"] ?? null) === "yes"
                && ($job->filters["q"] ?? null) === "export-verified");
    }

    /**
     * @return void
     */
    public function test_admin_users_export_without_status_filter_excludes_inactive_users(): void
    {
        $inactive = User::factory()->create([
            "name" => "inactive-export-user",
            "email" => "inactive-export-user@example.com",
            "email_verified_at" => now(),
            "tenant_id" => null,
        ]);
        $inactive->delete();

        $path = app(UserAdminRepository::class)->exportToFile("csv", []);
        $contents = file_get_contents($path);

        $this->assertIsString($contents);
        $this->assertStringNotContainsString("inactive-export-user@example.com", $contents);
    }

    /**
     * @return void
     */
    public function test_admin_users_export_with_status_all_includes_inactive_users(): void
    {
        $inactive = User::factory()->create([
            "name" => "inactive-export-all-user",
            "email" => "inactive-export-all-user@example.com",
            "email_verified_at" => now(),
            "tenant_id" => null,
        ]);
        $inactive->delete();

        $path = app(UserAdminRepository::class)->exportToFile("csv", [
            "status" => "all",
        ]);
        $contents = file_get_contents($path);

        $this->assertIsString($contents);
        $this->assertStringContainsString("inactive-export-all-user@example.com", $contents);
    }

    /**
     * @return void
     */
    public function test_admin_users_stats_registrations(): void
    {
        $this->getJson("/api/v1/admin/users/stats/registrations")
            ->assertStatus(200)
            ->assertJsonStructure([
                "labels",
                "series",
            ]);
    }

    /**
     * @return void
     */
    public function test_admin_users_stats_roles(): void
    {
        $this->getJson("/api/v1/admin/users/stats/roles")
            ->assertStatus(200)
            ->assertJsonStructure([
                "labels",
                "series",
            ]);
    }
}
