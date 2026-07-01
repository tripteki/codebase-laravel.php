<?php

namespace Modules\Event\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Event\App\Models\Event;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenancyForApi
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantKey = $request->route("tenant");

        if (! is_string($tenantKey) || $tenantKey === "") {
            abort(404, __("event.not_found"));
        }

        $event = Event::query()->find($tenantKey);

        if ($event === null) {
            abort(404, __("event.not_found"));
        }

        tenancy()->initialize($event);
        sync_permissions_team_context();

        return $next($request);
    }
}
