<?php

namespace Src\V0\Log\Tests\Feature\Log;

use Tripteki\Helpers\Traits\UserFactoryTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogTest extends TestCase
{
    use RefreshDatabase, UserFactoryTrait;

    /**
     * @return void
     */
    public function test_users_can_viewAny_log()
    {
        $user = $this->user();
        $this->actingAs($user);

        $data = $this->get(/* config("adminer.route.user") ?? */"api"."/logs");
        $data->assertStatus(200);
    }
};
