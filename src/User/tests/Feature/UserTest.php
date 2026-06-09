<?php

namespace Modules\User\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Acl\App\Enums\RoleEnum;
use Tests\TestCase;

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
        $this->user->assignRole(RoleEnum::ADMIN->value);

        $this->actingAsJwt($this->user);
    }

    /**
     * @return void
     */
    public function test_users_show(): void
    {
        $test = $this->getJson("/api/v1/users/me");

        $test->assertStatus(200)
            ->assertJsonStructure([
                "id",
                "name",
                "email",
                "email_verified_at",
                "created_at",
                "updated_at",
                "profile",
            ]);
    }

    /**
     * @return void
     */
    public function test_users_update(): void
    {
        $this->putJson("/api/v1/users/me", [
            "name" => "Updated Name",
            "email" => $this->user->email,
            "full_name" => "Updated Full Name",
            "interests" => [ "Design", "Product", ],
        ])
            ->assertStatus(200)
            ->assertJsonPath("name", "Updated Name")
            ->assertJsonPath("profile.full_name", "Updated Full Name")
            ->assertJsonPath("profile.interests", [ "Design", "Product", ]);
    }

    /**
     * @return void
     */
    public function test_users_interests(): void
    {
        $this->putJson("/api/v1/users/me", [
            "name" => $this->user->name,
            "email" => $this->user->email,
            "full_name" => "Interest User",
            "interests" => [ "Design", "Product", ],
        ])->assertStatus(200);

        $this->getJson("/api/v1/users/me/interests")
            ->assertStatus(200)
            ->assertJsonPath("data", [ "Design", "Product", ]);
    }

    /**
     * @return void
     */
    public function test_users_accesses(): void
    {
        $test = $this->getJson("/api/v1/users/me/accesses");

        $test->assertStatus(200)
            ->assertJsonStructure([
                "permissions",
                "roles",
            ])
            ->assertJsonPath("roles.0", RoleEnum::ADMIN->value);
    }
}
