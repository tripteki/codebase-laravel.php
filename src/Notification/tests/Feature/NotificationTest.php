<?php

namespace Modules\Notification\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \App\Models\User
     */
    protected $user;

    /**
     * @var \Illuminate\Notifications\DatabaseNotification
     */
    protected $notification;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAsJwt($this->user);

        $this->notification = $this->user->notifications()->create([
            "id" => \Illuminate\Support\Str::uuid()->toString(),
            "type" => \Illuminate\Notifications\Notification::class,
            "data" => [],
        ]);
    }

    /**
     * @return void
     */
    public function test_notifications_index(): void
    {
        $test = $this->getJson("/api/v1/notifications");

        $test->assertStatus(200)
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
    public function test_notifications_readall(): void
    {
        $test = $this->putJson("/api/v1/notifications/read-all");

        $test->assertStatus(200)
            ->assertJsonStructure([ "count", ]);
    }

    /**
     * @return void
     */
    public function test_notifications_read(): void
    {
        $test = $this->putJson("/api/v1/notifications/read/".$this->notification->id);

        $test->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_notifications_count(): void
    {
        $test = $this->getJson("/api/v1/notifications/count");

        $test->assertStatus(200)
            ->assertJsonStructure([ "count", ]);
    }

    /**
     * @return void
     */
    public function test_notifications_unread(): void
    {
        $test = $this->getJson("/api/v1/notifications/unread");

        $test->assertStatus(200)
            ->assertJsonStructure([ "unread", ]);
    }

    /**
     * @return void
     */
    public function test_notifications_show(): void
    {
        $test = $this->getJson("/api/v1/notifications/".$this->notification->id);

        $test->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_notifications_destroy(): void
    {
        $test = $this->deleteJson("/api/v1/notifications/".$this->notification->id);

        $test->assertStatus(200);
    }
}
