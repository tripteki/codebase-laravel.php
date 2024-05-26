<?php

namespace Src\V1\Sample\Providers;

use Illuminate\Support\ServiceProvider as ServiceProvider;

class SampleBaseServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    public static $version = "v1";

    /**
     * @var string
     */
    public static $moduleName = "Sample";

    /**
     * @var string
     */
    public static $moduleNameLower = "sample";

    /**
     * @return void
     */
    public function register()
    {
        $this->app->register(SampleRouteServiceProvider::class);
        $this->app->register(SampleEventListenerServiceProvider::class);
    }

    /**
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->registerTranslations();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path(self::$version, self::$moduleName."/Database/Migrations"));
    }

    /**
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes(
        [
            module_path(self::$version, self::$moduleName."/Config/config.php") => config_path(self::$moduleNameLower.".php"),

        ], "config");

        $this->mergeConfigFrom(module_path(self::$version, self::$moduleName."/Config/config.php"), self::$moduleNameLower);
    }

    /**
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path("lang/modules/".self::$moduleNameLower);

        if (is_dir($langPath)) {

            $this->loadTranslationsFrom($langPath, self::$moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath, self::$moduleNameLower);

        } else {

            $this->loadTranslationsFrom(module_path(self::$version, self::$moduleName."/Lang"), self::$moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path(self::$version, self::$moduleName."/Lang"), self::$moduleNameLower);
        }
    }

    /**
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path("views/modules/".self::$moduleNameLower);
        $sourcePath = module_path(self::$version, self::$moduleName."/Resources/views");

        $this->publishes(
        [
            $sourcePath => $viewPath,

        ], [ "views", self::$moduleNameLower."-module-views", ]);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [ $sourcePath, ]), self::$moduleNameLower);
    }

    /**
     * @return array
     */
    private function getPublishableViewPaths()
    {
        $paths = [];

        foreach (config("view.paths") as $path) {

            if (is_dir($path."/modules/".self::$moduleNameLower)) {

                $paths[] = $path."/modules/".self::$moduleNameLower;
            }
        }

        return $paths;
    }
};
