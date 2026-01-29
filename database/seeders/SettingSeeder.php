<?php

namespace Database\Seeders;

use App\Helpers\SettingHelper;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $settings = [
            [
                "key" => "app_name",
                "value" => config("app.name"),
            ],
            [
                "key" => "app_version",
                "value" => config("app.version", "1.0.0"),
            ],
        ];

        foreach ($settings as $setting) {
            SettingHelper::set(
                $setting["key"],
                $setting["value"]
            );
        }
    }
}
