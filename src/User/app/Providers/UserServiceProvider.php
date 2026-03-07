<?php

namespace Modules\User\App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Event;
use Modules\User\App\Events\UserAdminActivated;
use Modules\User\App\Events\UserAdminDeactivated;
use Modules\User\App\Events\UserAdminExported;
use Modules\User\App\Events\UserAdminExportedFailed;
use Modules\User\App\Events\UserAdminImported;
use Modules\User\App\Events\UserAdminImportedFailed;
use Modules\User\App\Listeners\UserAdminAccountMailListener;
use Modules\User\App\Listeners\UserAdminOperationDatabaseListener;
use Modules\User\App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(module_path("User", "Database/migrations"));
        $this->registerTranslations();

        Gate::policy(User::class, UserPolicy::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Modules\User\App\Console\UserCleanCommand::class,
            ]);
        }

        $databaseListener = UserAdminOperationDatabaseListener::class;
        $mailListener = UserAdminAccountMailListener::class;

        Event::listen(UserAdminImported::class, [ $databaseListener, "handleImported", ]);
        Event::listen(UserAdminImportedFailed::class, [ $databaseListener, "handleImportedFailed", ]);
        Event::listen(UserAdminExported::class, [ $databaseListener, "handleExported", ]);
        Event::listen(UserAdminExportedFailed::class, [ $databaseListener, "handleExportedFailed", ]);
        Event::listen(UserAdminActivated::class, [ $mailListener, "handleActivated", ]);
        Event::listen(UserAdminDeactivated::class, [ $mailListener, "handleDeactivated", ]);
    }

    /**
     * @return void
     */
    protected function registerTranslations(): void
    {
        $langPath = module_path("User", "lang");

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
