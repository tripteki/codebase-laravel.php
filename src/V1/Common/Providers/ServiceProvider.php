<?php

namespace Src\V1\Common\Providers;

use Src\V1\Common\Traits\ServiceProviderTrait;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    use ServiceProviderTrait;

    /**
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->registerTranslations();
        $this->registerMigrations();
    }

    /**
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom($this->modulePath("/Config/config.php"), $this->moduleLower());
    }

    /**
     * @return void
     */
    public function registerTranslations()
    {
        $this->loadTranslationsFrom($this->modulePath("/Lang"), $this->moduleLower());
        $this->loadJsonTranslationsFrom($this->modulePath("/Lang"), $this->moduleLower());
    }

    /**
     * @return void
     */
    public function registerMigrations()
    {
        $this->loadMigrationsFrom($this->modulePath("/Database/Migrations"));
    }
};
