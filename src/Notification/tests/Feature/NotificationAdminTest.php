<?php

namespace Modules\Notification\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Acl\Database\Seeders\AclSeeder;
use Modules\Notification\Database\Seeders\NotificationSeeder;
use Modules\User\Database\Seeders\CreateUserSeeder;
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

        $this->withoutAdminApiMiddleware();
        $this->enablePermissionTeams();

        $this->artisan("db:seed", [ "--class" => AclSeeder::class, ]);
        $this->artisan("db:seed", [ "--class" => UserSeeder::class, ]);
        $this->artisan("db:seed", [ "--class" => NotificationSeeder::class, ]);

        $this->user = $this->resolveCentralAdmin();

        $this->actingAsJwt($this->user, CreateUserSeeder::DEFAULT_PASSWORD);

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
        $this->getJson("/api/v1/admin/notifications")
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
}
