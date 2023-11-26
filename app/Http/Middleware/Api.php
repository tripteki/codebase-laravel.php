<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Api
{
    /**
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @param string|null ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $request->headers->set("Accept", "application/json");

        return $next($request);
    }
}
