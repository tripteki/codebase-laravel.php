<?php

namespace Modules\Notification\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Event\App\Enums\AddOnEnum;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \App\Models\User
     */
    protected User $centralUser;

    /**
     * @var \App\Models\User
     */
    protected User $tenantUser;

    /**
     * @var string
     */
    protected string $password = "Password123!";

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutAdminApiMiddleware();

        $this->centralUser = User::factory()->create([
            "password" => Hash::make($this->password),
            "email_verified_at" => now(),
        ]);

        $this->tenantId = "notification-tenant";

        $this->createTenantEvent($this->tenantId, [
            "title" => "Notification Tenant",
            "add_ons_modules" => [
                AddOnEnum::MODULES_NOTIFICATION->value,
            ],
        ]);

        $this->tenantUser = User::factory()->create([
            "password" => Hash::make($this->password),
            "email_verified_at" => now(),
            "tenant_id" => $this->tenantId,
        ]);

        if (function_exists("tenancy") && tenancy()->initialized) {
            tenancy()->end();
            sync_permissions_team_context();
        }
    }

    /**
     * @param \App\Models\User $user
     * @return \Illuminate\Notifications\DatabaseNotification
     */
    protected function createNotification(User $user): DatabaseNotification
    {
        return $user->notifications()->create([
            "id" => Str::uuid()->toString(),
            "type" => Notification::class,
            "data" => [],
        ]);
    }

    /**
     * @return void
     */
    public function test_notifications_index(): void
    {
        $this->createNotification($this->centralUser);

        $this->authGet(
            "/api/v1/notifications",
            $this->loginToken($this->centralUser, $this->password),
        )->assertStatus(200)
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

        $this->createNotification($this->tenantUser);

        $this->authGet(
            "/api/v1/notifications",
            $this->loginToken($this->tenantUser, $this->password),
        )->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_notifications_readall(): void
    {
        $this->createNotification($this->centralUser);

        $this->authPut(
            "/api/v1/notifications/read-all",
            $this->loginToken($this->centralUser, $this->password),
        )
            ->assertStatus(200)
            ->assertJsonStructure([ "count", ]);

        $this->createNotification($this->tenantUser);

        $this->authPut(
            "/api/v1/notifications/read-all",
            $this->loginToken($this->tenantUser, $this->password),
        )
            ->assertStatus(200)
            ->assertJsonStructure([ "count", ]);
    }

    /**
     * @return void
     */
    public function test_notifications_read(): void
    {
        $centralNotification = $this->createNotification($this->centralUser);

        $this->authPut(
            "/api/v1/notifications/read/".$centralNotification->id,
            $this->loginToken($this->centralUser, $this->password),
        )->assertStatus(200);

        $tenantNotification = $this->createNotification($this->tenantUser);

        $this->authPut(
            "/api/v1/notifications/read/".$tenantNotification->id,
            $this->loginToken($this->tenantUser, $this->password),
        )->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_notifications_count(): void
    {
        $this->createNotification($this->centralUser);

        $this->authGet(
            "/api/v1/notifications/count",
            $this->loginToken($this->centralUser, $this->password),
        )
            ->assertStatus(200)
            ->assertJsonStructure([ "count", ]);

        $this->createNotification($this->tenantUser);

        $this->authGet(
            "/api/v1/notifications/count",
            $this->loginToken($this->tenantUser, $this->password),
        )
            ->assertStatus(200)
            ->assertJsonStructure([ "count", ]);
    }

    /**
     * @return void
     */
    public function test_notifications_unread(): void
    {
        $this->createNotification($this->centralUser);

        $this->authGet(
            "/api/v1/notifications/unread",
            $this->loginToken($this->centralUser, $this->password),
        )
            ->assertStatus(200)
            ->assertJsonStructure([ "unread", ]);

        $this->createNotification($this->tenantUser);

        $this->authGet(
            "/api/v1/notifications/unread",
            $this->loginToken($this->tenantUser, $this->password),
        )
            ->assertStatus(200)
            ->assertJsonStructure([ "unread", ]);
    }

    /**
     * @return void
     */
    public function test_notifications_show(): void
    {
        $centralNotification = $this->createNotification($this->centralUser);

        $this->authGet(
            "/api/v1/notifications/".$centralNotification->id,
            $this->loginToken($this->centralUser, $this->password),
        )->assertStatus(200);

        $tenantNotification = $this->createNotification($this->tenantUser);

        $this->authGet(
            "/api/v1/notifications/".$tenantNotification->id,
            $this->loginToken($this->tenantUser, $this->password),
        )->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_notifications_destroy(): void
    {
        $centralNotification = $this->createNotification($this->centralUser);

        $this->authDelete(
            "/api/v1/notifications/".$centralNotification->id,
            $this->loginToken($this->centralUser, $this->password),
        )->assertStatus(200);

        $tenantNotification = $this->createNotification($this->tenantUser);

        $this->authDelete(
            "/api/v1/notifications/".$tenantNotification->id,
            $this->loginToken($this->tenantUser, $this->password),
        )->assertStatus(200);
    }
}
