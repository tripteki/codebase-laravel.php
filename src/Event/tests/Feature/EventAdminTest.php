<?php

namespace Modules\Event\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Acl\App\Models\Role;
use Modules\Acl\Database\Seeders\AclSeeder;
use Modules\Event\App\Enums\AddOnEnum;
use Modules\Event\App\Jobs\EventAdminExportJob;
use Modules\Event\App\Models\Event;
use Modules\Event\Database\Seeders\EventSeeder;
use Modules\User\App\Enums\UserEnum;
use Modules\User\Database\Seeders\CreateUserSeeder;
use Modules\User\Database\Seeders\UserSeeder;
use Tests\TestCase;

class EventAdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    protected User $superadmin;

    /**
     * @var User
     */
    protected User $admin;

    /**
     * @var string
     */
    protected string $token;

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

        sync_permissions_team_context();

        $this->superadmin = $this->resolveCentralAdmin();
        $this->token = $this->loginToken($this->superadmin, CreateUserSeeder::DEFAULT_PASSWORD);

        $this->admin = User::factory()->create([
            "password" => Hash::make("Password123!"),
            "email_verified_at" => now(),
        ]);

        $this->assignCentralRole($this->admin, RoleEnum::ADMIN->value);
    }

    /**
     * @return void
     */
    public function test_get_post_put_delete_admin_events_on_central_routes(): void
    {
        $this->authGet(
            $this->centralAdminApi("/events"),
            $this->loginToken($this->superadmin, CreateUserSeeder::DEFAULT_PASSWORD),
        )
            ->assertStatus(200)
            ->assertJsonStructure(["totalPage", "perPage", "data"]);

        $this->authPostJson(
            $this->centralAdminApi("/events"),
            $this->loginToken($this->superadmin, CreateUserSeeder::DEFAULT_PASSWORD),
            [
                "id" => "demo-event",
                "title" => "Demo Event",
                "description" => "Demo description",
                "primary_color" => "#112233",
            ],
        )
            ->assertStatus(201)
            ->assertJsonPath("id", "demo-event")
            ->assertJsonPath("title", "Demo Event");

        $this->assertDatabaseHas("users", [
            "email" => RoleEnum::ADMIN->value.".demo-event@".config("app.email_server"),
            "tenant_id" => "demo-event",
        ]);

        tenancy()->initialize(Event::query()->findOrFail("demo-event"));
        sync_permissions_team_context();

        $tenantAdmin = User::query()->where(
            "email",
            RoleEnum::ADMIN->value.".demo-event@".config("app.email_server"),
        )->firstOrFail();

        $this->assertTrue($tenantAdmin->hasRole(
            Role::findByName(RoleEnum::ADMIN->value, GuardEnum::WEB->value),
        ));

        $this->assertDatabaseMissing("roles", [
            "name" => RoleEnum::SUPERADMIN->value,
            "tenant_id" => "demo-event",
        ]);

        tenancy()->end();
        sync_permissions_team_context();

        $this->authGet(
            $this->centralAdminApi("/events/demo-event"),
            $this->loginToken($this->superadmin, CreateUserSeeder::DEFAULT_PASSWORD),
        )
            ->assertStatus(200)
            ->assertJsonPath("title", "Demo Event");

        $this->authPut(
            $this->centralAdminApi("/events/demo-event"),
            $this->loginToken($this->superadmin, CreateUserSeeder::DEFAULT_PASSWORD),
            [
                "title" => "Updated Demo Event",
            ],
        )
            ->assertStatus(200)
            ->assertJsonPath("title", "Updated Demo Event");

        $this->authDelete(
            $this->centralAdminApi("/events/demo-event"),
            $this->loginToken($this->superadmin, CreateUserSeeder::DEFAULT_PASSWORD),
        )
            ->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_get_admin_events_returns_403_without_superadmin(): void
    {
        $adminToken = $this->loginToken($this->admin, "Password123!");

        $this->authGet($this->centralAdminApi("/events"), $adminToken)
            ->assertStatus(403);
    }

    /**
     * @return void
     */
    public function test_get_admin_events_stats_overview(): void
    {
        $this->authGet($this->centralAdminApi("/events/stats/overview"), $this->token)
            ->assertStatus(200)
            ->assertJsonStructure(["labels", "series"]);
    }

    /**
     * @return void
     */
    public function test_post_admin_events_persists_selected_modules_without_merge(): void
    {
        $this->authPostJson(
            $this->centralAdminApi("/events"),
            $this->loginToken($this->superadmin, CreateUserSeeder::DEFAULT_PASSWORD),
            [
                "id" => "core-modules-only",
                "title" => "Core Modules Only",
                "add_ons_modules" => [AddOnEnum::MODULES_USER->value],
                "add_ons_features" => [AddOnEnum::FEATURES_EXPORT->value],
            ],
        )
            ->assertStatus(201)
            ->assertJsonPath("add_ons_modules", [
                AddOnEnum::MODULES_USER->value,
            ])
            ->assertJsonMissing([AddOnEnum::MODULES_ACL->value])
            ->assertJsonMissing([AddOnEnum::MODULES_LOG->value]);
    }

    /**
     * @return void
     */
    public function test_post_admin_events_applies_default_modules_when_omitted(): void
    {
        $this->authPostJson(
            $this->centralAdminApi("/events"),
            $this->loginToken($this->superadmin, CreateUserSeeder::DEFAULT_PASSWORD),
            [
                "id" => "default-modules-event",
                "title" => "Default Modules Event",
                "add_ons_features" => [AddOnEnum::FEATURES_EXPORT->value],
            ],
        )
            ->assertStatus(201)
            ->assertJsonPath("add_ons_modules", AddOnEnum::defaultModuleValues());
    }

    /**
     * @return void
     */
    public function test_post_admin_events_persists_copyright_image(): void
    {
        $this->authPost(
            $this->centralAdminApi("/events"),
            $this->loginToken($this->superadmin, CreateUserSeeder::DEFAULT_PASSWORD),
            [
                "id" => "copyright-image-event",
                "title" => "Copyright Image Event",
                "copyright_mode" => "image",
                "copyright_image" => \Illuminate\Http\UploadedFile::fake()->image("copyright.png"),
            ],
        )
            ->assertStatus(201)
            ->assertJsonPath("copyright_text", null)
            ->assertJsonStructure(["copyright_image_url"]);

        $this->assertNotNull(
            Event::query()->findOrFail("copyright-image-event")->getAttribute("copyright_image"),
        );
    }

    /**
     * @return void
     */
    public function test_post_admin_events_update_persists_copyright_image_via_multipart(): void
    {
        $this->createTenantEvent("copyright-update-event", [
            "title" => "Copyright Update Event",
        ]);

        $this->authPost(
            $this->centralAdminApi("/events/copyright-update-event"),
            $this->token,
            [
                "title" => "Copyright Update Event",
                "copyright_mode" => "image",
                "copyright_image" => \Illuminate\Http\UploadedFile::fake()->image("copyright.png"),
            ],
        )
            ->assertStatus(200)
            ->assertJsonStructure(["copyright_image_url"]);

        $this->assertNotNull(
            Event::query()->findOrFail("copyright-update-event")->getAttribute("copyright_image"),
        );
    }

    /**
     * @return void
     */
    public function test_admin_events_export_respects_filters(): void
    {
        Queue::fake();

        $this->authPost(
            $this->centralAdminApi("/events/export?type=csv&filters=q:Demo,id:demo-event"),
            $this->token,
        )->assertStatus(200);

        Queue::assertPushed(EventAdminExportJob::class, fn (EventAdminExportJob $job): bool => ($job->filters["q"] ?? null) === "Demo"
                && ($job->filters["id"] ?? null) === "demo-event");
    }

    /**
     * @return void
     */
    public function test_central_superadmin_email_is_not_tenant_scoped(): void
    {
        $this->assertDatabaseHas("users", [
            "email" => UserEnum::SUPERUSER->value."@".config("app.email_server"),
            "tenant_id" => null,
        ]);
    }
}
