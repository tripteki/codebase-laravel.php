<?php

namespace Src\V0\Auth\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends BaseController
{
    /**
     * @OA\Post(
     *      path="/auth/forgot-password",
     *      tags={"Auth"},
     *      summary="Send credential reset link",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      description="User's Email."
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity."
     *      )
     * )
     *
     * Handle an incoming password reset link.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response|JsonResponse|RedirectResponse
    {
        $request->validate([
            "email" => [ "required", "email", ],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only("email")
        );

        if ($request->wantsJson()) {

            if ($status != Password::RESET_LINK_SENT) {
                throw ValidationException::withMessages([
                    "email" => [__($status)],
                ]);
            }

            return iresponse([ "status" => __($status), ], Response::HTTP_OK);

        } else {

            return $status == Password::RESET_LINK_SENT
                    ? back()->with("status", __($status))
                    : back()->withInput($request->only("email"))
                            ->withErrors(["email" => __($status)]);
        }
    }
}
