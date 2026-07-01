<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCentralAdmin
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! is_tenancy()) {
            sync_permissions_team_context();

            return $next($request);
        }

        if (! is_central()) {
            return $next($request);
        }

        sync_permissions_team_context();

        $user = $request->user();

        if ($user === null || ! is_central_superadmin($user)) {
            abort(403, __("route.forbidden"));
        }

        return $next($request);
    }
}
