<?php

namespace Src\V0\Auth\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller as BaseController;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class EmailVerificationNotificationController extends BaseController
{
    /**
     * @OA\Post(
     *      path="/auth/email/verification-notification",
     *      tags={"Auth"},
     *      summary="Re-Verification",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      )
     * )
     *
     * Send a new email verification notification.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): Response|JsonResponse|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {

            if ($request->wantsJson()) {

                return iresponse([ "status" => "verified", ], Response::HTTP_OK);

            } else {

                return redirect()->intended(RouteServiceProvider::HOME);
            }
        }

        $request->user()->sendEmailVerificationNotification();

        if ($request->wantsJson()) {

            return iresponse([ "status" => __("auth.verification-sent"), ], Response::HTTP_OK);

        } else {

            return back()->with("status", __("auth.verification-sent"));
        }
    }
}
