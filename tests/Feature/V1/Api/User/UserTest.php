<?php

namespace Tests\Feature\V1\Api\User;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

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
    public function test_users_index(): void
    {
        $test = $this->getJson("/api/v1/users");

        $test->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_users_show(): void
    {
        $test = $this->getJson("/api/v1/users/me");

        $test->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_users_update(): void
    {
        $test = $this->putJson("/api/v1/users", [

            "name" => "user",
            "email" => "user@mail.com",
            "password" => "12345678",
        ]);

        $test->assertStatus(201);
    }

    /**
     * @return void
     */
    public function test_users_destroy(): void
    {
        $test = $this->deleteJson("/api/v1/users");

        $test->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_users_store(): void
    {
        $test = $this->test_users_destroy();
        $test = $this->postJson("/api/v1/users");

        $test->assertStatus(201);
    }
}
