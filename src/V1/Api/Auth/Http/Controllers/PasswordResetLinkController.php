<?php

namespace Src\V1\Api\Auth\Http\Controllers;

use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Src\V1\Api\Common\Http\Controllers\Controller as BaseController;

class PasswordResetLinkController extends BaseController
{
    /**
     * @OA\Post(
     *      path="/api/v1/auth/forgot-password",
     *      tags={"Auth"},
     *      summary="Send Credential Reset Link",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      description="Email"
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
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

        if ($status != Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                "email" => [ __($status), ],
            ]);
        }

        return response()->json([ "status" => __($status), ], 200);
    }
}
