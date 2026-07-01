<?php

namespace Tests\Unit;

use App\Helpers\AppNameHelper;
use App\Helpers\BrandingHelper;
use App\Helpers\SettingHelper;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandingHelperTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_resolve_uses_settings_and_app_display_name(): void
    {
        SettingHelper::set("COLOR_PRIMARY", "#111111");
        SettingHelper::set("COLOR_SECONDARY", "#222222");
        SettingHelper::set("COLOR_TERTIARY", "#333333");

        $branding = BrandingHelper::resolve();

        $this->assertSame(AppNameHelper::headline(), $branding["displayName"]);
        $this->assertSame("#111111", $branding["primaryColor"]);
        $this->assertSame("#222222", $branding["secondaryColor"]);
        $this->assertSame("#333333", $branding["tertiaryColor"]);
        $this->assertSame(AppNameHelper::headline(), $branding["appName"]);
    }

    /**
     * @return void
     */
    public function test_resolve_logo_url_from_setting_file(): void
    {
        Setting::query()->create([
            "key" => "LOGO",
            "value" => "setting-files/logo.png",
        ]);

        $branding = BrandingHelper::resolve();

        $this->assertStringContainsString("storage/setting-files/logo.png", $branding["logoUrl"]);
    }
}
