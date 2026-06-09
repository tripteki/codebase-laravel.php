<?php

namespace Modules\Notification\App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Notification\App\Models\Notification;
use Modules\Notification\App\Observers\NotificationObserver;
use Modules\Notification\App\Policies\NotificationPolicy;

class NotificationServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(module_path("Notification", "Database/migrations"));
        $this->registerTranslations();

        Gate::policy(Notification::class, NotificationPolicy::class);
        Notification::observe(NotificationObserver::class);
    }

    /**
     * @return void
     */
    protected function registerTranslations(): void
    {
        $langPath = module_path("Notification", "lang");

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
