<?php

namespace Modules\User\Tests\Feature;

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

        $this->artisan("db:seed", ["--class" => \Modules\Acl\Database\Seeders\AclSeeder::class]);

        $this->user = User::factory()->create([
            "email_verified_at" => now(),
        ]);
        $this->actingAsJwt($this->user);
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
