<?php

namespace Src\V1\Api\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Src\V1\Api\Common\Http\Controllers\Controller as BaseController;

class EmailVerificationNotificationController extends BaseController
{
    /**
     * @OA\Post(
     *      path="/api/v1/auth/email/verification-notification",
     *      tags={"Auth"},
     *      summary="Re-Verification",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      )
     * )
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) return response()->json(__("auth.verified"), 200);
            $user->sendEmailVerificationNotification();

        return response()->json(__("auth.verification-sent"), 200);
    }
}
