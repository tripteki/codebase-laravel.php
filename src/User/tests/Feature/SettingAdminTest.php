<?php

namespace Modules\User\Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Acl\Database\Seeders\AclSeeder;
use Modules\User\Database\Seeders\CreateUserSeeder;
use Modules\User\Database\Seeders\SettingSeeder;
use Modules\User\Database\Seeders\UserSeeder;
use Tests\TestCase;

class SettingAdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    protected User $centralAdmin;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutAdminApiMiddleware();

        $this->artisan("db:seed", [ "--class" => AclSeeder::class, ]);
        $this->artisan("db:seed", [ "--class" => UserSeeder::class, ]);
        $this->artisan("db:seed", [ "--class" => SettingSeeder::class, ]);

        $this->centralAdmin = $this->resolveCentralAdmin();
    }

    /**
     * @return void
     */
    public function test_get_admin_settings(): void
    {
        $this->authGet(
            $this->centralAdminApi("/settings"),
            $this->loginToken($this->centralAdmin, CreateUserSeeder::DEFAULT_PASSWORD),
        )
            ->assertStatus(200)
            ->assertJsonFragment([
                "key" => "COLOR_PRIMARY",
                "value" => "#2563eb",
            ]);
    }

    /**
     * @return void
     */
    public function test_put_admin_settings(): void
    {
        $setting = Setting::query()->where("key", "COLOR_PRIMARY")->firstOrFail();

        $this->authPut(
            $this->centralAdminApi("/settings"),
            $this->loginToken($this->centralAdmin, CreateUserSeeder::DEFAULT_PASSWORD),
            [
                "rows" => [
                    [
                        "id" => $setting->id,
                        "key" => "COLOR_PRIMARY",
                        "value" => "#111827",
                        "value_kind" => "text",
                    ],
                ],
            ],
        )
            ->assertStatus(200)
            ->assertJsonFragment([
                "key" => "COLOR_PRIMARY",
                "value" => "#111827",
            ]);

        $this->assertDatabaseHas("settings", [
            "key" => "COLOR_PRIMARY",
            "value" => "#111827",
        ]);
    }

    /**
     * @return void
     */
    public function test_admin_settings_forbidden_for_non_superadmin(): void
    {
        $admin = User::factory()->create([
            "email_verified_at" => now(),
        ]);
        $admin->assignRole("admin");

        $this->authGet(
            $this->centralAdminApi("/settings"),
            $this->loginToken($admin, "password"),
        )->assertStatus(403);
    }
}
