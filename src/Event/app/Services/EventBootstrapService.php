<?php

namespace Modules\Event\App\Services;

use Modules\Event\App\Enums\AddOnEnum;
use Modules\Event\App\Models\Event;
use Modules\Event\App\Support\AddOnsHelper;
use Modules\Event\Database\Seeders\EventTenantBootstrapSeeder;
use Modules\User\Database\Seeders\SyncTenantBrandingSettingsSeeder;

class EventBootstrapService
{
    /**
     * @param Event $event
     * @return void
     */
    public function bootstrap(Event $event): void
    {
        $this->runInTenantContext($event, function (): void {
            activity()->disableLogging();
            app(EventTenantBootstrapSeeder::class)->run();
        });
    }

    /**
     * @param Event $event
     * @return void
     */
    public function syncBrandingSettings(Event $event): void
    {
        $this->runInTenantContext($event, function (): void {
            app(SyncTenantBrandingSettingsSeeder::class)->run();
        });
    }

    /**
     * @param Event $event
     * @param list<string> $previousRawModules
     * @return void
     */
    public function syncNewlyEnabledModules(Event $event, array $previousRawModules): void
    {
        $currentRawModules = AddOnsHelper::parseList($event->getAttribute("add_ons_modules"));

        foreach (AddOnEnum::moduleValues() as $moduleValue) {
            $module = AddOnEnum::tryFromValue($moduleValue);

            if ($module === null) {
                continue;
            }

            if (
                in_array($moduleValue, $previousRawModules, true)
                || ! in_array($moduleValue, $currentRawModules, true)
            ) {
                continue;
            }

            $this->runInTenantContext($event, function () use ($module): void {
                activity()->disableLogging();
                app(EventTenantBootstrapSeeder::class)->seedModule($module);
            });
        }
    }

    /**
     * @param Event $event
     * @param callable(): void $callback
     * @return void
     */
    private function runInTenantContext(Event $event, callable $callback): void
    {
        tenancy()->initialize($event);

        try {
            sync_permissions_team_context();
            $callback();
        } finally {
            activity()->enableLogging();
            tenancy()->end();
            sync_permissions_team_context();
        }
    }
}
