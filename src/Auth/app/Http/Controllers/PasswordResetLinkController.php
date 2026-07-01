<?php

namespace Modules\Auth\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Auth\App\Events\UserAuthReset;
use Modules\Auth\App\Mail\ResetPasswordMail;
use Modules\Auth\App\Support\PasswordResetTokenHelper;
use Modules\Event\App\Support\AuthAddOnsHelper;
use Modules\Event\App\Support\TenantMailHelper;
use Modules\User\App\Dtos\UserTransformerDto;
use OpenApi\Attributes as OA;

class PasswordResetLinkController extends BaseController
{
    #[OA\Post(
        path: "/api/v1/auth/forgot-password",
        tags: ["UserAuth"],
        summary: "Forgot Password",
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "application/x-www-form-urlencoded",
                schema: new OA\Schema(
                    required: ["email"],
                    properties: [
                        new OA\Property(property: "email", type: "string", description: "Email"),
                        new OA\Property(property: "tenant", type: "string", description: "Tenant slug"),
                    ],
                ),
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
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            "email" => [ "required", "email", ],
            "tenant" => [ "nullable", "string", ],
        ]);

        $email = $request->email;
        $tenantId = $request->string("tenant")->toString() ?: null;
        $tenantId = is_string($tenantId) && trim($tenantId) !== "" ? trim($tenantId) : null;

        AuthAddOnsHelper::initializeFromTenantId($tenantId);
        AuthAddOnsHelper::abortIfPasswordResetDisabled();

        $userQuery = User::query()
            ->withoutTenancy()
            ->where("email", $email);

        if ($tenantId !== null) {
            $userQuery->where("tenant_id", $tenantId);
        } else {
            $userQuery->whereNull("tenant_id");
        }

        $user = $userQuery->first();

        if ($user) {
            $urlTenantId = $tenantId ?? (
                $user->tenant_id !== null ? (string) $user->tenant_id : null
            );

            $signedUrl = signed_auth_frontend_url(
                $urlTenantId,
                auth_reset_password_path($email),
            );
            parse_str((string) parse_url($signedUrl, PHP_URL_QUERY), $query);
            $signed = $query["signed"] ?? null;

            if (is_string($signed) && $signed !== "") {
                PasswordResetTokenHelper::upsertSigned($email, $signed, $urlTenantId);

                TenantMailHelper::send(
                    $email,
                    fn () => new ResetPasswordMail(
                        userName: $user->displayName(),
                        userNameLabel: $user->displayNameLabel(),
                        userEmail: $email,
                        resetLink: $signedUrl,
                    ),
                    $urlTenantId,
                );

                event(new UserAuthReset(
                    UserTransformerDto::fromUser($user),
                    $signed,
                ));
            }
        }

        return response()->json(__("passwords.sent"), 200);
    }
}
