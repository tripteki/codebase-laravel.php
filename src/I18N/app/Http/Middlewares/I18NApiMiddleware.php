<?php

namespace Modules\I18N\App\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Modules\Event\App\Enums\AddOnEnum;
use Modules\Event\App\Support\AddOnsHelper;
use Modules\I18N\App\Services\I18NService;
use Symfony\Component\HttpFoundation\Response;

class I18NApiMiddleware
{
    /**
     * @var \Modules\I18N\App\Services\I18NService
     */
    protected $i18nService;

    /**
     * @param \Modules\I18N\App\Services\I18NService $i18nService
     * @return void
     */
    public function __construct(I18NService $i18nService)
    {
        $this->i18nService = $i18nService;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string ...$guards
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        if ($request->expectsJson()) {
            if ($this->shouldRestrictLocaleToFallback()) {
                App::setLocale($this->i18nService->fallbackLang());
            } else {
                $this->i18nService->getLanguageFromQueryString($request) ??
                $this->i18nService->getLanguageFromAcceptHeader($request) ??
                $this->i18nService->getLanguageFromCustomHeader($request);
            }
        }

        return $next($request);
    }

    /**
     * @return bool
     */
    private function shouldRestrictLocaleToFallback(): bool
    {
        if (! function_exists("tenancy") || ! tenancy()->initialized) {
            return false;
        }

        return ! AddOnsHelper::has(AddOnEnum::FEATURES_MULTI_LANGUAGE);
    }
}
