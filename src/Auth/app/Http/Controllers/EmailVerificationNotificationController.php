<?php

namespace Modules\Auth\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Modules\Auth\App\Events\UserAuthReverify;
use Modules\User\App\Dtos\UserTransformerDto;
use App\Http\Controllers\Controller as BaseController;
use OpenApi\Attributes as OA;

class EmailVerificationNotificationController extends BaseController
{
    #[OA\Post(
        path: "/api/v1/auth/email/verification-notification",
        tags: ["UserAuth"],
        summary: "Re-Verification",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Success."),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(__("auth.verified"), 200);
        }

        $user->sendEmailVerificationNotification();

        event(new UserAuthReverify(UserTransformerDto::fromUser($user)));

        return response()->json(__("auth.verification-sent"), 200);
    }
}
