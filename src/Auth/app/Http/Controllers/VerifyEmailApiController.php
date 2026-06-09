<?php

namespace Modules\Auth\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\User\App\Dtos\UserTransformerDto;
use OpenApi\Attributes as OA;

class VerifyEmailApiController extends BaseController
{
    #[OA\Post(
        path: "/api/v1/auth/verify-email/{email}",
        tags: ["UserAuth"],
        summary: "Verify Email",
        parameters: [
            new OA\Parameter(name: "email", in: "path", required: true, description: "Email address"),
            new OA\Parameter(name: "signed", in: "query", required: true, description: "Signed verification token"),
        ],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Invalid token."),
            new OA\Response(response: 404, description: "Not Found."),
        ],
    )]
    /**
     * @param \Illuminate\Http\Request $request
     * @param string $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, string $email): JsonResponse
    {
        if (! verify_auth_signed_url(auth_verify_email_path($email), $request->query("signed"))) {
            abort(403, __("auth.token_invalid"));
        }

        $user = User::query()->where("email", $email)->firstOrFail();

        if ($user->hasVerifiedEmail()) {
            return response()->json(UserTransformerDto::fromUser($user), 200);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(UserTransformerDto::fromUser($user->fresh()), 200);
    }
}
