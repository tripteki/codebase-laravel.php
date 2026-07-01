<?php

namespace Modules\Event\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Event\App\Models\Event;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenancyForAuthenticatedUser
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (tenancy()->initialized) {
            return $next($request);
        }

        $user = $request->user();
        $tenantId = $user?->tenant_id;

        if ($tenantId === null || $tenantId === "") {
            return $next($request);
        }

        $event = Event::query()->find($tenantId);

        if ($event === null) {
            abort(404, __("event.not_found"));
        }

        tenancy()->initialize($event);
        sync_permissions_team_context();

        return $next($request);
    }
}
