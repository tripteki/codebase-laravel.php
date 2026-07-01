<?php

namespace Modules\Auth\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Auth\App\Events\UserAuthReverify;
use Modules\Event\App\Support\AuthAddOnsHelper;
use Modules\User\App\Dtos\UserTransformerDto;
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
        ],
    )]
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        AuthAddOnsHelper::initializeForUser($user);
        AuthAddOnsHelper::abortIfEmailVerificationDisabled();

        if ($user->hasVerifiedEmail()) {
            return response()->json(__("auth.verified"), 200);
        }

        $user->sendEmailVerificationNotification();

        event(new UserAuthReverify(UserTransformerDto::fromUser($user)));

        return response()->json(__("auth.verification-sent"), 200);
    }
}
