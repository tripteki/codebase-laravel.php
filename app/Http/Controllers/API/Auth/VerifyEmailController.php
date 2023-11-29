<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authed user's email address as verified.
     *
     * @param \Illuminate\Foundation\Auth\EmailVerificationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->route("id")) Auth::guard("web")->login(User::findOrFail($request->route("id")));
        else abort(Response::HTTP_UNAUTHORIZED);

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(
                ($request->wantsJson() ? config("app.frontend_url") : "").RouteServiceProvider::HOME."?verified=1"
            );
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(
            ($request->wantsJson() ? config("app.frontend_url") : "").RouteServiceProvider::HOME."?verified=1"
        );
    }
}
