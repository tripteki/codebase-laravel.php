<?php

namespace Modules\User\Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $settings = [
            [ "key" => "COLOR_PRIMARY", "value" => "#2563eb", ],
            [ "key" => "COLOR_SECONDARY", "value" => "#84cc16", ],
            [ "key" => "COLOR_TERTIARY", "value" => "#1e3a8a", ],
        ];

        foreach ($settings as $setting) {
            Setting::query()->updateOrCreate(
                [ "key" => $setting["key"], ],
                [ "value" => $setting["value"], ],
            );
        }
    }
}
