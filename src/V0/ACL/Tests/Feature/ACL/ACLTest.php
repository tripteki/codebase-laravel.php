<?php

namespace Src\V0\ACL\Tests\Feature\ACL;

use Tripteki\Helpers\Traits\UserFactoryTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ACLTest extends TestCase
{
    use RefreshDatabase, UserFactoryTrait;

    /**
     * @return void
     */
    public function test_users_can_viewAny_rule()
    {
        $user = $this->user();
        $this->actingAs($user);

        $data = accesses();
        $this->assertIsArray($data);
    }
};
