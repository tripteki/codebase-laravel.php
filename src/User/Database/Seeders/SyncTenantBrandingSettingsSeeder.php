<?php

namespace Modules\User\Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Modules\Event\App\Models\Event;

class SyncTenantBrandingSettingsSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        if (! function_exists("tenant")) {
            return;
        }

        $event = tenant();

        if (! $event instanceof Event) {
            return;
        }

        $settings = [
            "COLOR_PRIMARY" => $event->getAttribute("primary_color"),
            "COLOR_SECONDARY" => $event->getAttribute("secondary_color"),
            "COLOR_TERTIARY" => $event->getAttribute("tertiary_color"),
        ];

        foreach ($settings as $key => $value) {
            if ($value === null || trim((string) $value) === "") {
                continue;
            }

            Setting::query()->updateOrCreate(
                ["key" => $key],
                ["value" => (string) $value],
            );
        }
    }
}
