<?php

namespace Modules\Log\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Acl\Database\Seeders\AclSeeder;
use Modules\Log\App\Models\Activity;
use Modules\Log\Database\Seeders\LogSeeder;
use Modules\User\Database\Seeders\CreateUserSeeder;
use Modules\User\Database\Seeders\UserSeeder;
use Tests\InteractsWithTenancy;
use Tests\TestCase;

class ActivityAdminTest extends TestCase
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
        $this->artisan("db:seed", [ "--class" => LogSeeder::class, ]);

        $this->user = $this->resolveCentralAdmin();

        $this->actingAsJwt($this->user, CreateUserSeeder::DEFAULT_PASSWORD);
    }

    /**
     * @return void
     */
    public function test_admin_activities_index(): void
    {
        Activity::query()->create([
            "log_name" => "default",
            "description" => "updated profile",
            "subject_type" => User::class,
            "subject_id" => $this->user->getKey(),
            "causer_type" => User::class,
            "causer_id" => $this->user->getKey(),
            "event" => "updated",
            "properties" => [ "attributes" => [ "name" => "Updated", ], ],
        ]);

        $this->getJson($this->centralAdminApi("/activities?filters=q:updated"))
            ->assertStatus(200)
            ->assertJsonStructure([
                "totalPage",
                "perPage",
                "currentPage",
                "data",
            ])
            ->assertJsonFragment([ "description" => "updated profile", ]);
    }

    /**
     * @return void
     */
    public function test_central_admin_activities_index_includes_tenant_activities_without_tenant_filter(): void
    {
        $this->createTenantEvent("tenant-activities-list", [
            "title" => "Tenant Activities List",
        ]);

        Activity::query()->create([
            "log_name" => "default",
            "description" => "tenant activity entry",
            "subject_type" => User::class,
            "subject_id" => $this->user->getKey(),
            "causer_type" => User::class,
            "causer_id" => $this->user->getKey(),
            "event" => "updated",
            "tenant_id" => "tenant-activities-list",
            "properties" => [ "attributes" => [ "name" => "Tenant", ], ],
        ]);

        Activity::query()->create([
            "log_name" => "default",
            "description" => "central activity entry",
            "subject_type" => User::class,
            "subject_id" => $this->user->getKey(),
            "causer_type" => User::class,
            "causer_id" => $this->user->getKey(),
            "event" => "updated",
            "tenant_id" => null,
            "properties" => [ "attributes" => [ "name" => "Central", ], ],
        ]);

        $this->getJson($this->centralAdminApi("/activities?filters=q:tenant%20activity%20entry"))
            ->assertOk()
            ->assertJsonFragment([ "description" => "tenant activity entry", ]);

        $this->getJson($this->centralAdminApi("/activities?filters=q:central%20activity%20entry"))
            ->assertOk()
            ->assertJsonFragment([ "description" => "central activity entry", ]);
    }

    /**
     * @return void
     */
    public function test_central_admin_activities_filters_by_tenant(): void
    {
        $this->createTenantEvent("tenant-activities-filter", [
            "title" => "Tenant Activities Filter",
        ]);

        Activity::query()->create([
            "log_name" => "default",
            "description" => "tenant filtered activity",
            "subject_type" => User::class,
            "subject_id" => $this->user->getKey(),
            "causer_type" => User::class,
            "causer_id" => $this->user->getKey(),
            "event" => "updated",
            "tenant_id" => "tenant-activities-filter",
            "properties" => [],
        ]);

        Activity::query()->create([
            "log_name" => "default",
            "description" => "central filtered activity",
            "subject_type" => User::class,
            "subject_id" => $this->user->getKey(),
            "causer_type" => User::class,
            "causer_id" => $this->user->getKey(),
            "event" => "updated",
            "tenant_id" => null,
            "properties" => [],
        ]);

        $this->getJson($this->centralAdminApi("/activities?filters=tenant_id:tenant-activities-filter,q:tenant%20filtered"))
            ->assertOk()
            ->assertJsonFragment([ "description" => "tenant filtered activity", ])
            ->assertJsonMissing([ "description" => "central filtered activity", ]);

        $this->getJson($this->centralAdminApi("/activities?filters=tenant_id:central,q:central%20filtered"))
            ->assertOk()
            ->assertJsonFragment([ "description" => "central filtered activity", ])
            ->assertJsonMissing([ "description" => "tenant filtered activity", ]);
    }

    /**
     * @return void
     */
    public function test_admin_activities_show(): void
    {
        $activity = Activity::query()->create([
            "log_name" => "default",
            "description" => "created account",
            "subject_type" => User::class,
            "subject_id" => $this->user->getKey(),
            "causer_type" => User::class,
            "causer_id" => $this->user->getKey(),
            "event" => "created",
            "properties" => [ "attributes" => [ "email" => $this->user->email, ], ],
        ]);

        $this->getJson($this->centralAdminApi("/activities/".$activity->getKey()))
            ->assertStatus(200)
            ->assertJsonPath("description", "created account");
    }
}
