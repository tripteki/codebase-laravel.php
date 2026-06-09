<?php

namespace Modules\Auth\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Modules\Auth\App\Events\UserAuthReset;
use Modules\Auth\App\Mail\ResetPasswordMail;
use Modules\Auth\App\Support\PasswordResetTokenHelper;
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
        ]);

        $email = $request->email;
        $user = User::query()->where("email", $email)->first();

        if ($user) {
            $signedUrl = signed_frontend_url(auth_reset_password_path($email));
            parse_str((string) parse_url($signedUrl, PHP_URL_QUERY), $query);
            $signed = $query["signed"] ?? null;

            if (is_string($signed) && $signed !== "") {
                PasswordResetTokenHelper::upsertSigned($email, $signed);

                Mail::to($email)->send(new ResetPasswordMail(
                    userName: $user->displayName(),
                    userNameLabel: $user->displayNameLabel(),
                    userEmail: $email,
                    resetLink: $signedUrl,
                ));

                event(new UserAuthReset(
                    UserTransformerDto::fromUser($user),
                    $signed,
                ));
            }
        }

        return response()->json(__("passwords.sent"), 200);
    }
}
