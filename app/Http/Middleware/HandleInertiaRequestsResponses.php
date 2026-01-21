<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Src\V1\Api\I18N\Services\I18NService;

class HandleInertiaRequestsResponses extends Middleware
{
    /**
     * The root template that's loaded on the initial page visit.
     */
    protected $rootView = "app";

    /**
     * Determines the current asset version.
     */
    public function version(Request $request): ?string
    {
        return $response = parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @param Request $request
     * @return array
     */
    public function share(Request $request): array
    {
        $i18nService = app(I18NService::class);
        $currentLang = $i18nService->getLanguageFromSession($request);

        return $response = array_merge(parent::share($request), [

            "lang" => $currentLang,
            "fallbackLang" => $i18nService->fallbackLang(),
            "availableLangs" => $i18nService->availableLangs(),
            "translations" => $this->getTranslations($currentLang),
            "appName" => Str::headline(config("app.name")),
            "appVersion" => config("app.version"),
            "authUser" => fn () => $request->user(),
        ]);
    }

    /**
     * Get translations for the given language.
     *
     * @param string $lang
     * @return array
     */
    protected function getTranslations(string $lang): array
    {
        $translations = [];

        // Load all translation files from lang directory
        $langPath = lang_path($lang);

        if (is_dir($langPath)) {
            foreach (glob($langPath."/*.php") as $file) {
                $key = basename($file, ".php");
                $translations[$key] = require $file;
            }
        }

        return $translations;
    }
}
