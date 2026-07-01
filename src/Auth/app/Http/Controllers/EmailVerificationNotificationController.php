<?php

namespace Modules\Auth\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

        $user->sendEmailVerificationNotification();

        event(new UserAuthReverify(UserTransformerDto::fromUser($user)));

        return response()->json(__("auth.verification_email_sent"), 200);
    }

    #[OA\Post(
        path: "/api/v1/auth/resend-verification",
        tags: ["UserAuth"],
        summary: "Resend Email Verification",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email"),
                ],
            ),
        ),
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 422, description: "Validation Error."),
        ],
    )]
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(Request $request): JsonResponse
    {
        Validator::make($request->all(), [
            "email" => [ "required", "email:rfc,filter", ],
        ])->validate();

        $email = trim((string) $request->input("email"));

        AuthAddOnsHelper::initializeFromTenantId(null);

        if (AuthAddOnsHelper::isMailingEnabled()) {
            $user = User::query()
                ->withoutTenancy()
                ->where("email", $email)
                ->whereNull("tenant_id")
                ->first();

            if ($user !== null && ! $user->hasVerifiedEmail()) {
                $user->sendEmailVerificationNotification();
            }
        }

        return response()->json(__("auth.verification_email_sent"), 200);
    }
}
