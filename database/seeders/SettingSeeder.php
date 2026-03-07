<?php

namespace Database\Seeders;

use App\Helpers\SettingHelper;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $settings = [

            ["key" => "OWNER", "value" => "Trip Teknologi"],
            ["key" => "ALL_RIGHTS_RESERVED", "value" => ""],

            ["key" => "COLOR_PRIMARY", "value" => "#2563eb"], // hsl(213.5, 96.4%, 44.1%) //
            ["key" => "COLOR_SECONDARY", "value" => "#84cc16"], // hsl(83.8, 80.5%, 44.3%) //
            ["key" => "COLOR_TERTIARY", "value" => "#1e3a8a"], // hsl(219.5, 95.2%, 24.7%) //

            ["key" => "AUTH_TENANCY", "value" => false],
            ["key" => "CONTENT_AUTH_BRAND_DESCRIPTION", "value" => ""],
            ["key" => "CONTENT_AUTH_EVENT_LIVE", "value" => ""],
            ["key" => "CONTENT_AUTH_INTERACTIVE_TECH", "value" => ""],
            ["key" => "CONTENT_AUTH_ECOSYSTEM", "value" => ""],
            ["key" => "CONTENT_AUTH_WELCOME_BACK_MESSAGE", "value" => ""],
        ];

        foreach ($settings as $setting) {
            SettingHelper::set(
                $setting["key"],
                $setting["value"]
            );
        }
    }
}
