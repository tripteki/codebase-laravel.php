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
            new OA\Parameter(name: "tenant", in: "query", required: false, description: "Tenant slug"),
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
        $tenantId = $request->query("tenant");
        $tenantId = is_string($tenantId) && trim($tenantId) !== "" ? trim($tenantId) : null;

        if (! verify_auth_signed_url($tenantId, auth_verify_email_path($email), $request->query("signed"))) {
            abort(403, __("auth.token_invalid"));
        }

        $userQuery = User::query()
            ->withoutTenancy()
            ->where("email", $email);

        if ($tenantId !== null) {
            $userQuery->where("tenant_id", $tenantId);
        } else {
            $userQuery->whereNull("tenant_id");
        }

        $user = $userQuery->firstOrFail();

        if ($user->hasVerifiedEmail()) {
            return response()->json(UserTransformerDto::fromUser($user), 200);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(UserTransformerDto::fromUser($user->fresh()), 200);
    }
}
