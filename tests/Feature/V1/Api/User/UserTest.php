<?php

namespace Tests\Feature\V1\Api\User;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \App\Models\User
     */
    protected $user;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user, "api");
    }

    /**
     * @return void
     */
    public function test_users_show(): void
    {
        $test = $this->getJson("/api/v1/users/me");

        $test->assertStatus(200);
    }
}
