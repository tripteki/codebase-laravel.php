<?php

namespace Modules\Auth\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Auth\App\Enums\UserAuthTokenScopeEnum;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class EnsureJwtScopeMiddleware
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $scope
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $scope = "ACCESS_TOKEN"): Response
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();
        } catch (\Throwable) {
            abort(401, __("auth.not_authorized"));
        }

        if ($payload->get("scope") !== $scope) {
            abort(401, __("auth.not_authorized"));
        }

        return $next($request);
    }
}
