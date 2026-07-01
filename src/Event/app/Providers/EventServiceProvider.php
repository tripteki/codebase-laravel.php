<?php

namespace Modules\Event\App\Providers;

use Illuminate\Support\Facades\Event as EventDispatcher;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Event\App\Events\EventAdminExported;
use Modules\Event\App\Events\EventAdminExportedFailed;
use Modules\Event\App\Events\EventAdminImported;
use Modules\Event\App\Events\EventAdminImportedFailed;
use Modules\Event\App\Listeners\EventAdminOperationDatabaseListener;
use Modules\Event\App\Models\Event;
use Modules\Event\App\Policies\EventPolicy;

class EventServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path("Event", "Database/migrations"));
        $this->registerTranslations();

        Gate::policy(Event::class, EventPolicy::class);

        $databaseListener = EventAdminOperationDatabaseListener::class;

        EventDispatcher::listen(EventAdminImported::class, [$databaseListener, "handleImported"]);
        EventDispatcher::listen(EventAdminImportedFailed::class, [$databaseListener, "handleImportedFailed"]);
        EventDispatcher::listen(EventAdminExported::class, [$databaseListener, "handleExported"]);
        EventDispatcher::listen(EventAdminExportedFailed::class, [$databaseListener, "handleExportedFailed"]);
    }

    /**
     * @return void
     */
    protected function registerTranslations(): void
    {
        $langPath = module_path("Event", "lang");

        if (! is_dir($langPath)) {
            return;
        }

        $translator = $this->app["translator"];

        foreach (glob($langPath."/*", GLOB_ONLYDIR) ?: [] as $localePath) {
            $locale = basename($localePath);

            foreach (glob($localePath."/*.php") ?: [] as $file) {
                $group = basename($file, ".php");
                $lines = require $file;
                $prefixed = [];

                $this->flattenTranslations($lines, "{$group}.", $prefixed);
                $translator->addLines($prefixed, $locale);
            }
        }
    }

    /**
     * @param array<string, mixed> $lines
     * @param string $prefix
     * @param array<string, mixed> $prefixed
     * @return void
     */
    protected function flattenTranslations(array $lines, string $prefix, array &$prefixed): void
    {
        foreach ($lines as $key => $value) {
            if (is_array($value)) {
                $this->flattenTranslations($value, $prefix.$key.".", $prefixed);

                continue;
            }

            $prefixed[$prefix.$key] = $value;
        }
    }
}
