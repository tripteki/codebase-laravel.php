<?php

namespace Modules\User\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Database\Seeders\SettingSeeder;
use Tests\TestCase;

class SettingVariablesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SettingSeeder::class);
    }

    /**
     * @return void
     */
    public function test_get_v1_settings_variables(): void
    {
        $this->getJson("/api/v1/settings/variables")
            ->assertStatus(200)
            ->assertJsonPath("data.COLOR_PRIMARY", "#2563eb")
            ->assertJsonPath("data.EMAIL_SERVER", config("app.email_server"));
    }
}
