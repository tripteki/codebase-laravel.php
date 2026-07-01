<?php

namespace Modules\Acl\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Jobs\RoleAdminExportJob;
use Modules\Acl\App\Models\Permission;
use Modules\Acl\App\Models\Role;
use Modules\Acl\Database\Seeders\AclSeeder;
use Modules\Event\App\Enums\AddOnEnum;
use Modules\User\App\Enums\PermissionEnum as UserPermissionEnum;
use Modules\User\Database\Seeders\CreateUserSeeder;
use Modules\User\Database\Seeders\UserSeeder;
use Tests\InteractsWithTenancy;
use Tests\TestCase;

class RoleAdminTest extends TestCase
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
    public function test_admin_roles_index(): void
    {
        Role::create([
            "name" => "custom-manager",
            "guard_name" => GuardEnum::WEB->value,
        ]);

        $this->getJson($this->centralAdminApi("/roles?filters=q:custom-manager"))
            ->assertStatus(200)
            ->assertJsonStructure([
                "totalPage",
                "perPage",
                "currentPage",
                "data",
            ])
            ->assertJsonFragment([ "name" => "custom-manager", ]);
    }

    /**
     * @return void
     */
    public function test_central_admin_roles_index_includes_tenant_roles_without_tenant_filter(): void
    {
        $this->createTenantEvent("tenant-roles-list", [
            "title" => "Tenant Roles List",
        ]);

        Role::create([
            "name" => "tenant-only-role",
            "guard_name" => GuardEnum::WEB->value,
            "tenant_id" => "tenant-roles-list",
        ]);

        Role::create([
            "name" => "central-only-role",
            "guard_name" => GuardEnum::WEB->value,
        ]);

        $this->getJson($this->centralAdminApi("/roles"))
            ->assertOk()
            ->assertJsonFragment([ "name" => "tenant-only-role", ])
            ->assertJsonFragment([ "name" => "central-only-role", ]);
    }

    /**
     * @return void
     */
    public function test_admin_roles_store(): void
    {
        $permission = Permission::query()
            ->where("name", UserPermissionEnum::USER_VIEW->value)
            ->firstOrFail();

        $this->postJson($this->centralAdminApi("/roles"), [
            "tenant" => "central",
            "name" => "custom-editor",
            "guard_name" => GuardEnum::WEB->value,
            "permission_ids" => [ (string) $permission->getKey(), ],
        ])
            ->assertStatus(201)
            ->assertJsonPath("name", "custom-editor");
    }

    /**
     * @return void
     */
    public function test_admin_roles_store_for_tenant_event(): void
    {
        $this->createTenantEvent("tenant-role-create", [
            "title" => "Tenant Role Create",
        ]);

        $this->postJson($this->centralAdminApi("/roles"), [
            "tenant" => "tenant-role-create",
            "name" => "tenant-editor",
            "guard_name" => GuardEnum::WEB->value,
        ])
            ->assertStatus(201)
            ->assertJsonPath("tenant_id", "tenant-role-create");
    }

    /**
     * @return void
     */
    public function test_admin_roles_show(): void
    {
        $role = Role::create([
            "name" => "custom-showable",
            "guard_name" => GuardEnum::WEB->value,
        ]);

        $this->getJson($this->centralAdminApi("/roles/".$role->getKey()))
            ->assertStatus(200)
            ->assertJsonPath("name", "custom-showable");
    }

    /**
     * @return void
     */
    public function test_admin_roles_update(): void
    {
        $role = Role::create([
            "name" => "custom-updatable",
            "guard_name" => GuardEnum::WEB->value,
        ]);

        $this->putJson($this->centralAdminApi("/roles/".$role->getKey()), [
            "name" => "custom-updated",
        ])
            ->assertStatus(200)
            ->assertJsonPath("name", "custom-updated");
    }

    /**
     * @return void
     */
    public function test_admin_roles_destroy(): void
    {
        $role = Role::create([
            "name" => "custom-deletable",
            "guard_name" => GuardEnum::WEB->value,
        ]);

        $this->deleteJson($this->centralAdminApi("/roles/".$role->getKey()))
            ->assertStatus(200);

        $this->assertDatabaseMissing("roles", [ "id" => $role->getKey(), ]);
    }

    /**
     * @return void
     */
    public function test_admin_roles_import_and_export(): void
    {
        $permission = UserPermissionEnum::USER_VIEW->value;
        $csv = "name,guard_name,permissions\ncustom-imported,web,{$permission}";

        $this->post($this->centralAdminApi("/roles/import"), [
            "file" => UploadedFile::fake()->createWithContent("roles.csv", $csv),
        ])->assertStatus(200);

        $this->assertDatabaseHas("roles", [ "name" => "custom-imported", ]);

        $this->postJson($this->centralAdminApi("/roles/export?type=csv"))
            ->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_admin_roles_export_respects_filters(): void
    {
        Queue::fake();

        $this->postJson(
            $this->centralAdminApi("/roles/export?type=csv&filters=q:custom-imported,guard_name:web"),
        )->assertStatus(200);

        Queue::assertPushed(RoleAdminExportJob::class, fn (RoleAdminExportJob $job): bool => ($job->filters["q"] ?? null) === "custom-imported"
                && ($job->filters["guard_name"] ?? null) === "web");
    }

    /**
     * @return void
     */
    public function test_admin_roles_export_respects_tenant_filter(): void
    {
        Queue::fake();

        $this->postJson(
            $this->centralAdminApi("/roles/export?type=csv&filters=tenant_id:tenant-role-create"),
        )->assertStatus(200);

        Queue::assertPushed(RoleAdminExportJob::class, fn (RoleAdminExportJob $job): bool => ($job->filters["tenant_id"] ?? null) === "tenant-role-create");
    }

    /**
     * @return void
     */
    public function test_tenant_admin_role_import_assigns_tenant_id(): void
    {
        $this->tenantId = "tenant-role-import";

        $this->createTenantEvent($this->tenantId, [
            "title" => "Tenant Role Import",
            "add_ons_features" => [
                AddOnEnum::FEATURES_IMPORT->value,
                AddOnEnum::FEATURES_EXPORT->value,
            ],
        ]);

        $tenantAdmin = User::query()->where(
            "email",
            "admin.".$this->tenantId."@".config("app.email_server"),
        )->firstOrFail();

        $csv = "name,guard_name,permissions\ntenant-imported-role,web,";

        $this->post(
            $this->adminApi("/roles/import"),
            [
                "file" => UploadedFile::fake()->createWithContent("roles.csv", $csv),
            ],
            [
                "Authorization" => "Bearer ".$this->loginToken($tenantAdmin, CreateUserSeeder::DEFAULT_PASSWORD),
            ],
        )->assertStatus(200);

        $this->assertDatabaseHas("roles", [
            "name" => "tenant-imported-role",
            "tenant_id" => $this->tenantId,
        ]);
    }

    /**
     * @return void
     */
    public function test_central_admin_role_import_assigns_tenant_from_column(): void
    {
        $this->createTenantEvent("tenant-role-csv-import", [
            "title" => "Tenant Role CSV Import",
        ]);

        $csv = "tenant,name,guard_name,permissions\ntenant-role-csv-import,central-csv-imported-role,web,";

        $this->post($this->centralAdminApi("/roles/import"), [
            "file" => UploadedFile::fake()->createWithContent("roles.csv", $csv),
        ])->assertStatus(200);

        $this->assertDatabaseHas("roles", [
            "name" => "central-csv-imported-role",
            "tenant_id" => "tenant-role-csv-import",
        ]);
    }

    /**
     * @return void
     */
    public function test_central_admin_role_import_assigns_tenant_from_xlsx(): void
    {
        $this->createTenantEvent("tenant-role-xlsx-import", [
            "title" => "Tenant Role XLSX Import",
        ]);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(["tenant", "name", "guard_name", "permissions"], null, "A1");
        $sheet->fromArray(["tenant-role-xlsx-import", "central-xlsx-imported-role", "web", ""], null, "A2");

        $tempPath = tempnam(sys_get_temp_dir(), "roles");
        $xlsxPath = $tempPath.".xlsx";
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($xlsxPath);

        $this->post($this->centralAdminApi("/roles/import"), [
            "file" => new UploadedFile(
                $xlsxPath,
                "roles.xlsx",
                "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                null,
                true,
            ),
        ])->assertStatus(200);

        $this->assertDatabaseHas("roles", [
            "name" => "central-xlsx-imported-role",
            "tenant_id" => "tenant-role-xlsx-import",
        ]);

        @unlink($xlsxPath);
    }
}
