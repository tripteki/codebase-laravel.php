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
     * @var string
     */
    protected $rootView = "app";

    /**
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return $response = parent::version($request);
    }

    /**
     * @return array
     */
    public function share(Request $request): array
    {
        $i18nService = app(I18NService::class);

        return $response = array_merge(parent::share($request), [

            "lang" => $i18nService->getLanguageFromSession($request),
            "fallbackLang" => $i18nService->fallbackLang(),
            "availableLangs" => $i18nService->availableLangs(),
            "appName" => Str::headline(config("app.name")),
            "appVersion" => config("app.version"),
            "authUser" => fn () => $request->user(),
        ]);
    }
}
