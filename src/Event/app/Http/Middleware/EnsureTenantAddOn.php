<?php

namespace Modules\Event\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Event\App\Enums\AddOnEnum;
use Modules\Event\App\Support\AddOnsHelper;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantAddOn
{
    /**
     * @param Request $request
     * @param Closure $next
     * @param string $addOn
     * @return Response
     */
    public function handle(Request $request, Closure $next, string $addOn): Response
    {
        if (! function_exists("tenancy") || ! tenancy()->initialized) {
            return $next($request);
        }

        $enum = AddOnEnum::tryFromValue($addOn);

        if ($enum === null || ! AddOnsHelper::has($enum)) {
            abort(403, __("event.add_ons.forbidden"));
        }

        return $next($request);
    }
}
