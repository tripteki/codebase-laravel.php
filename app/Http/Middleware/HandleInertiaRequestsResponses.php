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
     */
    public function share(Request $request): array
    {
        $i18nService = app(I18NService::class);

        return $response = array_merge(parent::share($request), [

            "lang" => $i18nService->getLanguageFromSession($request),
            "fallbackLang" => $i18nService->fallbackLang(),
            "availableLangs" => $i18nService->availableLangs(),
            "appName" => Str::headline(config("app.name")),
            "authUser" => fn () => $request->user(),
        ]);
    }
}
