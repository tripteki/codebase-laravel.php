<?php

namespace Modules\Event\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Acl\Database\Seeders\AclSeeder;
use Modules\Event\App\Enums\AddOnEnum;
use Modules\Event\Database\Seeders\EventSeeder;
use Modules\User\Database\Seeders\UserSeeder;
use Tests\TestCase;

class AddOnVariablesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan("db:seed", ["--class" => AclSeeder::class]);
        $this->artisan("db:seed", ["--class" => EventSeeder::class]);
        $this->artisan("db:seed", ["--class" => UserSeeder::class]);
    }

    /**
     * @return void
     */
    public function test_get_tenant_add_on_variables(): void
    {
        $tenantId = "addon-vars-event";

        $this->createTenantEvent($tenantId, [
            "title" => "Add-on Vars Event",
            "add_ons_features" => [AddOnEnum::FEATURES_EXPORT->value],
            "add_ons_modules" => [
                AddOnEnum::MODULES_USER->value,
                AddOnEnum::MODULES_NOTIFICATION->value,
            ],
        ]);

        $this->getJson("/api/v1/{$tenantId}/add-ons/variables")
            ->assertStatus(200)
            ->assertJsonStructure([
                "data" => [
                    "features",
                    "modules",
                    "branding" => [
                        "title",
                        "icon_url",
                        "favicon_ico_url",
                        "favicon_png_url",
                        "brand_light_url",
                        "brand_dark_url",
                        "copyright_text",
                        "copyright_image_url",
                    ],
                ],
            ])
            ->assertJsonFragment(["title" => "Add-on Vars Event"])
            ->assertJsonFragment([AddOnEnum::FEATURES_EXPORT->value])
            ->assertJsonFragment([AddOnEnum::MODULES_NOTIFICATION->value]);
    }

    /**
     * @return void
     */
    public function test_get_tenant_add_on_variables_returns_404_for_invalid_tenant(): void
    {
        $this->getJson("/api/v1/missing-event/add-ons/variables")
            ->assertStatus(404);
    }
}
