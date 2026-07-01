<?php

namespace Modules\Event\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Acl\App\Models\Permission;
use Modules\Acl\App\Models\Role;
use Modules\Acl\Database\Seeders\AclSeeder;
use Modules\User\App\Enums\UserEnum;
use Tests\TestCase;

class TenancyFoundationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->enablePermissionTeams();

        $this->artisan("db:seed", ["--class" => AclSeeder::class]);
        $this->artisan("db:seed", ["--class" => \Modules\User\Database\Seeders\CreateUserSeeder::class]);
    }

    /**
     * @return void
     */
    public function test_create_tenant_event_bootstraps_roles_and_admin_user(): void
    {
        $this->createTenantEvent("demo-event", [
            "title" => "Demo Event",
        ]);

        $this->assertDatabaseHas("tenants", ["id" => "demo-event"]);

        $this->withinTenant("demo-event", function (): void {
            $this->assertDatabaseHas("users", [
                "email" => RoleEnum::ADMIN->value.".demo-event@".config("app.email_server"),
                "tenant_id" => "demo-event",
            ]);

            $this->assertDatabaseHas("roles", [
                "name" => RoleEnum::ADMIN->value,
                "tenant_id" => "demo-event",
            ]);

            $this->assertDatabaseMissing("roles", [
                "name" => RoleEnum::SUPERADMIN->value,
                "tenant_id" => "demo-event",
            ]);
        });
    }

    /**
     * @return void
     */
    public function test_central_superadmin_exists_without_tenant_scope(): void
    {
        sync_permissions_team_context();

        $this->assertDatabaseHas("users", [
            "email" => UserEnum::SUPERUSER->value."@".config("app.email_server"),
            "tenant_id" => null,
        ]);

        $role = Role::findByName(RoleEnum::SUPERADMIN->value, GuardEnum::WEB->value);

        $this->assertNull($role->tenant_id);
    }

    /**
     * @return void
     */
    public function test_tenant_permissions_are_scoped_per_event(): void
    {
        $this->createTenantEvent("event-a");
        $this->createTenantEvent("event-b");

        $this->assertSame(1, Permission::query()
            ->where("name", "role.view")
            ->where("tenant_id", "event-a")
            ->count());

        $this->assertSame(1, Permission::query()
            ->where("name", "role.view")
            ->where("tenant_id", "event-b")
            ->count());
    }
}
