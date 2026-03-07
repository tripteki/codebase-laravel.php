<?php

namespace Modules\I18N\App\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
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
            $this->i18nService->getLanguageFromQueryString($request) ??
            $this->i18nService->getLanguageFromAcceptHeader($request) ??
            $this->i18nService->getLanguageFromCustomHeader($request);
        }

        return $next($request);
    }
}
