<?php

namespace Modules\User\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Modules\Acl\App\Enums\RoleEnum;
use Tests\TestCase;

class UserAdminTest extends TestCase
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

        $this->artisan("db:seed", ["--class" => \Modules\Acl\Database\Seeders\AclSeeder::class]);
        $this->artisan("db:seed", ["--class" => \Modules\User\Database\Seeders\UserSeeder::class]);

        $this->user = User::factory()->create([
            "password" => Hash::make("Password123!"),
            "email_verified_at" => now(),
        ]);

        $this->user->assignRole(RoleEnum::ADMIN->value);

        $this->actingAsJwt($this->user, "Password123!");
    }

    /**
     * @return void
     */
    public function test_admin_users_index(): void
    {
        $this->getJson("/api/v1/admin/users")
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
    public function test_admin_users_store(): void
    {
        $this->postJson("/api/v1/admin/users", [
            "name" => "Admin Created",
            "full_name" => "Admin Created Full Name",
            "email" => "admin-created@example.com",
            "password" => "Password123!",
            "password_confirmation" => "Password123!",
        ])->assertStatus(201)
            ->assertJsonPath("full_name", "Admin Created Full Name");
    }

    /**
     * @return void
     */
    public function test_admin_users_show(): void
    {
        $this->getJson("/api/v1/admin/users/".$this->user->id)->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_admin_users_deactivate_and_activate(): void
    {
        Mail::fake();

        $target = User::factory()->create([ "email_verified_at" => now(), ]);

        $this->deleteJson("/api/v1/admin/users/deactivate/".$target->id)->assertStatus(200);
        $this->deleteJson("/api/v1/admin/users/activate/".$target->id)->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_admin_users_force_delete(): void
    {
        Mail::fake();

        $target = User::factory()->create([ "email_verified_at" => now(), ]);

        $this->deleteJson("/api/v1/admin/users/deactivate/".$target->id)->assertStatus(200);
        $this->deleteJson("/api/v1/admin/users/force-delete/".$target->id)->assertStatus(200);

        $this->assertDatabaseMissing("users", [ "id" => $target->id, ]);
    }

    /**
     * @return void
     */
    public function test_admin_users_update(): void
    {
        $this->putJson("/api/v1/admin/users/".$this->user->id, [
            "name" => "Updated Name",
            "full_name" => "Updated Full Name",
        ])->assertStatus(200)
            ->assertJsonPath("name", "Updated Name")
            ->assertJsonPath("full_name", "Updated Full Name");
    }

    /**
     * @return void
     */
    public function test_admin_users_verify(): void
    {
        $target = User::factory()->create([ "email_verified_at" => null, ]);

        $this->putJson("/api/v1/admin/users/verify/".$target->id)
            ->assertStatus(200);

        $this->assertNotNull($target->fresh()->email_verified_at);
    }

    /**
     * @return void
     */
    public function test_admin_users_import(): void
    {
        $csv = "name,email,password\ntest-import,import-user@example.com,Password123!";

        $this->post("/api/v1/admin/users/import", [
            "file" => UploadedFile::fake()->createWithContent("users.csv", $csv),
        ])->assertStatus(200);

        $this->assertDatabaseHas("users", [ "email" => "import-user@example.com", ]);
        $this->assertDatabaseHas("profiles", [ "full_name" => "test-import", ]);
    }

    /**
     * @return void
     */
    public function test_admin_users_export(): void
    {
        $this->post("/api/v1/admin/users/export?type=csv")
            ->assertStatus(200);
    }
}
