<?php

namespace Modules\Auth\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Modules\Auth\App\Support\PasswordResetTokenHelper;
use Modules\User\App\Dtos\UserTransformerDto;
use OpenApi\Attributes as OA;

class ResetPasswordApiController extends BaseController
{
    #[OA\Post(
        path: "/api/v1/auth/reset-password/{email}",
        tags: ["UserAuth"],
        summary: "Reset Password (Signed Link)",
        parameters: [
            new OA\Parameter(name: "email", in: "path", required: true, description: "Email address"),
            new OA\Parameter(name: "signed", in: "query", required: true, description: "Signed reset token"),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "application/x-www-form-urlencoded",
                schema: new OA\Schema(
                    required: ["password", "password_confirmation"],
                    properties: [
                        new OA\Property(property: "password", type: "string", description: "Password"),
                        new OA\Property(property: "password_confirmation", type: "string", description: "Password Confirmation"),
                    ],
                ),
            ),
        ),
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 403, description: "Invalid token."),
            new OA\Response(response: 422, description: "Validation Error."),
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
        if (! verify_auth_signed_url(auth_reset_password_path($email), $request->query("signed"))) {
            abort(403, __("auth.token_invalid"));
        }

        $request->validate([
            "password" => [ "required", "confirmed", Rules\Password::defaults(), ],
        ]);

        $signed = $request->query("signed");

        $resetter = PasswordResetTokenHelper::findSigned($email, (string) $signed);

        if (! $resetter) {
            throw ValidationException::withMessages([
                "email" => [ __("passwords.token"), ],
            ]);
        }

        $user = User::query()->where("email", $email)->firstOrFail();
        $user->forceFill([
            "password" => Hash::make($request->password),
        ])->save();

        PasswordResetTokenHelper::delete($email);

        event(new PasswordReset($user));

        return response()->json(UserTransformerDto::fromUser($user->fresh()), 200);
    }
}
