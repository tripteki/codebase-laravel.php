<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * @var array<int, string>
     */
    protected $dontFlash = [
        "current_password",
        "password",
        "password_confirmation",
    ];

    /**
     * @return void
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception, \Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                return response()->json([
                    "message" => __("route.not_found"),
                ], 400);
            }
        });

        $this->renderable(function (\Illuminate\Database\Eloquent\ModelNotFoundException $exception, \Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                return response()->json([
                    "message" => __("route.model_not_found"),
                ], 404);
            }
        });

        $this->renderable(function (\Illuminate\Auth\AuthenticationException $exception, \Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                return response()->json([
                    "message" => __("route.unauthenticated"),
                ], 401);
            }
        });

        $this->renderable(function (\Illuminate\Validation\ValidationException $exception, \Illuminate\Http\Request $request) {
            if ($request->wantsJson() || $request->is("api/*")) {
                return response()->json([
                    "message" => __("route.unprocessable_entity"),
                    "errors" => $exception->errors(),
                ], 422);
            }
        });

        $this->renderable(function (AuthorizationException $exception, Request $request) {
            return $this->logoutWebAndRedirectOnForbidden($request);
        });

        $this->renderable(function (HttpException $exception, Request $request) {
            if ($exception->getStatusCode() !== 403) {
                return null;
            }

            return $this->logoutWebAndRedirectOnForbidden($request);
        });
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    protected function logoutWebAndRedirectOnForbidden(Request $request): ?Response
    {
        if ($request->is("api/*")) {
            return null;
        }

        if (! Auth::guard("web")->check()) {
            return null;
        }

        Auth::guard("web")->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                "message" => __("route.forbidden"),
            ], 403);
        }

        return redirect()->to(tenant_routes("admin.login"));
    }
}
