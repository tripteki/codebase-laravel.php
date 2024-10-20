<?php

namespace Src\V0\Notification\Tests\Feature\Notification;

use Tripteki\Helpers\Traits\UserFactoryTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase, UserFactoryTrait;

    /**
     * @return void
     */
    public function test_users_can_viewAny_notification()
    {
        $user = $this->user();
        $this->actingAs($user);

        $data = $this->get(/* config("adminer.route.user") ?? */"api"."/notifications");
        $data->assertStatus(200);
    }
};
