<?php

namespace Modules\Acl\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Jobs\PermissionAdminExportJob;
use Modules\Acl\App\Models\Permission;
use Modules\Acl\Database\Seeders\AclSeeder;
use Modules\Event\App\Enums\AddOnEnum;
use Modules\User\Database\Seeders\CreateUserSeeder;
use Modules\User\Database\Seeders\UserSeeder;
use Tests\InteractsWithTenancy;
use Tests\TestCase;

class PermissionAdminTest extends TestCase
{
    use InteractsWithTenancy,
        RefreshDatabase;

    /**
     * @var User
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

        $this->artisan("db:seed", [ "--class" => AclSeeder::class, ]);
        $this->artisan("db:seed", [ "--class" => UserSeeder::class, ]);

        $this->user = $this->resolveCentralAdmin();

        $this->actingAsJwt($this->user, CreateUserSeeder::DEFAULT_PASSWORD);
    }

    /**
     * @return void
     */
    public function test_admin_permissions_index(): void
    {
        Permission::create([
            "name" => "custom.report.view",
            "guard_name" => GuardEnum::WEB->value,
        ]);

        $this->getJson($this->centralAdminApi("/permissions?filters=q:custom.report"))
            ->assertStatus(200)
            ->assertJsonStructure([
                "totalPage",
                "perPage",
                "currentPage",
                "data",
            ])
            ->assertJsonFragment([ "name" => "custom.report.view", ]);
    }

    /**
     * @return void
     */
    public function test_central_admin_permissions_index_includes_tenant_permissions_without_tenant_filter(): void
    {
        $this->createTenantEvent("tenant-permissions-list", [
            "title" => "Tenant Permissions List",
        ]);

        Permission::create([
            "name" => "tenant.only.permission",
            "guard_name" => GuardEnum::WEB->value,
            "tenant_id" => "tenant-permissions-list",
        ]);

        Permission::create([
            "name" => "central.only.permission",
            "guard_name" => GuardEnum::WEB->value,
        ]);

        $this->getJson($this->centralAdminApi("/permissions?filters=q:tenant.only"))
            ->assertOk()
            ->assertJsonFragment([ "name" => "tenant.only.permission", ]);

        $this->getJson($this->centralAdminApi("/permissions?filters=q:central.only"))
            ->assertOk()
            ->assertJsonFragment([ "name" => "central.only.permission", ]);
    }

    /**
     * @return void
     */
    public function test_admin_permissions_store(): void
    {
        $this->postJson($this->centralAdminApi("/permissions"), [
            "tenant" => "central",
            "name" => "custom.report.export",
            "guard_name" => GuardEnum::WEB->value,
        ])
            ->assertStatus(201)
            ->assertJsonPath("name", "custom.report.export");
    }

    /**
     * @return void
     */
    public function test_admin_permissions_store_for_tenant_event(): void
    {
        $this->createTenantEvent("tenant-permission-create", [
            "title" => "Tenant Permission Create",
        ]);

        $this->postJson($this->centralAdminApi("/permissions"), [
            "tenant" => "tenant-permission-create",
            "name" => "tenant.custom.export",
            "guard_name" => GuardEnum::WEB->value,
        ])
            ->assertStatus(201)
            ->assertJsonPath("tenant_id", "tenant-permission-create");
    }

    /**
     * @return void
     */
    public function test_admin_permissions_show(): void
    {
        $permission = Permission::create([
            "name" => "custom.report.show",
            "guard_name" => GuardEnum::WEB->value,
        ]);

        $this->getJson($this->centralAdminApi("/permissions/".$permission->getKey()))
            ->assertStatus(200)
            ->assertJsonPath("name", "custom.report.show");
    }

    /**
     * @return void
     */
    public function test_admin_permissions_update(): void
    {
        $permission = Permission::create([
            "name" => "custom.report.edit",
            "guard_name" => GuardEnum::WEB->value,
        ]);

        $this->putJson($this->centralAdminApi("/permissions/".$permission->getKey()), [
            "name" => "custom.report.update",
        ])
            ->assertStatus(200)
            ->assertJsonPath("name", "custom.report.update");
    }

    /**
     * @return void
     */
    public function test_admin_permissions_destroy(): void
    {
        $permission = Permission::create([
            "name" => "custom.report.delete",
            "guard_name" => GuardEnum::WEB->value,
        ]);

        $this->deleteJson($this->centralAdminApi("/permissions/".$permission->getKey()))
            ->assertStatus(200);

        $this->assertDatabaseMissing("permissions", [ "id" => $permission->getKey(), ]);
    }

    /**
     * @return void
     */
    public function test_admin_permissions_import_and_export(): void
    {
        $csv = "name,guard_name\ncustom.imported.perm,web";

        $this->post($this->centralAdminApi("/permissions/import"), [
            "file" => UploadedFile::fake()->createWithContent("permissions.csv", $csv),
        ])->assertStatus(200);

        $this->assertDatabaseHas("permissions", [ "name" => "custom.imported.perm", ]);

        $this->postJson($this->centralAdminApi("/permissions/export?type=csv"))
            ->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_admin_permissions_export_respects_filters(): void
    {
        Queue::fake();

        $this->postJson(
            $this->centralAdminApi("/permissions/export?type=csv&filters=q:custom.imported,guard_name:web"),
        )->assertStatus(200);

        Queue::assertPushed(PermissionAdminExportJob::class, fn (PermissionAdminExportJob $job): bool => ($job->filters["q"] ?? null) === "custom.imported"
                && ($job->filters["guard_name"] ?? null) === "web");
    }

    /**
     * @return void
     */
    public function test_admin_permissions_export_respects_tenant_filter(): void
    {
        Queue::fake();

        $this->postJson(
            $this->centralAdminApi("/permissions/export?type=csv&filters=tenant_id:tenant-permission-create"),
        )->assertStatus(200);

        Queue::assertPushed(PermissionAdminExportJob::class, fn (PermissionAdminExportJob $job): bool => ($job->filters["tenant_id"] ?? null) === "tenant-permission-create");
    }

    /**
     * @return void
     */
    public function test_tenant_admin_permission_import_assigns_tenant_id(): void
    {
        $this->tenantId = "tenant-permission-import";

        $this->createTenantEvent($this->tenantId, [
            "title" => "Tenant Permission Import",
            "add_ons_features" => [
                AddOnEnum::FEATURES_IMPORT->value,
                AddOnEnum::FEATURES_EXPORT->value,
            ],
        ]);

        $tenantAdmin = User::query()->where(
            "email",
            "admin.".$this->tenantId."@".config("app.email_server"),
        )->firstOrFail();

        $csv = "name,guard_name\ntenant.imported.perm,web";

        $this->post(
            $this->adminApi("/permissions/import"),
            [
                "file" => UploadedFile::fake()->createWithContent("permissions.csv", $csv),
            ],
            [
                "Authorization" => "Bearer ".$this->loginToken($tenantAdmin, CreateUserSeeder::DEFAULT_PASSWORD),
            ],
        )->assertStatus(200);

        $this->assertDatabaseHas("permissions", [
            "name" => "tenant.imported.perm",
            "tenant_id" => $this->tenantId,
        ]);
    }

    /**
     * @return void
     */
    public function test_central_admin_permission_import_assigns_tenant_from_column(): void
    {
        $this->createTenantEvent("tenant-perm-csv-import", [
            "title" => "Tenant Permission CSV Import",
        ]);

        $csv = "tenant,name,guard_name\ntenant-perm-csv-import,central.csv.imported.perm,web";

        $this->post($this->centralAdminApi("/permissions/import"), [
            "file" => UploadedFile::fake()->createWithContent("permissions.csv", $csv),
        ])->assertStatus(200);

        $this->assertDatabaseHas("permissions", [
            "name" => "central.csv.imported.perm",
            "tenant_id" => "tenant-perm-csv-import",
        ]);
    }
}
