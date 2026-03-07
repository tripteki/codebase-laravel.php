<?php

namespace App\Http\Middleware;

use Closure;
use Src\V1\Api\Acl\Enums\RoleEnum;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCentralAdmin
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Request $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! is_central()) {
            return $next($request);
        }

        $user = $request->user();
        if (! $user) {
            return redirect()->to(tenant_routes("admin.login"));
        }

        if (! $user->hasRole(RoleEnum::SUPERADMIN->value)) {
            abort(403);
        }

        return $next($request);
    }
}
