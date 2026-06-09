<?php

namespace Modules\Auth\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Modules\Auth\App\Support\PasswordResetTokenHelper;
use Modules\User\App\Dtos\UserTransformerDto;
use OpenApi\Attributes as OA;

class NewPasswordController extends BaseController
{
    #[OA\Post(
        path: "/api/v1/auth/reset-password",
        tags: ["UserAuth"],
        summary: "Reset Password",
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "application/x-www-form-urlencoded",
                schema: new OA\Schema(
                    required: ["token", "email", "password", "password_confirmation"],
                    properties: [
                        new OA\Property(property: "token", type: "string", description: "Reset token"),
                        new OA\Property(property: "email", type: "string", description: "Email"),
                        new OA\Property(property: "password", type: "string", description: "Password"),
                        new OA\Property(property: "password_confirmation", type: "string", description: "Password Confirmation"),
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
            "token" => [ "required", "string", ],
            "email" => [ "required", "string", "email", ],
            "password" => [ "required", "confirmed", Rules\Password::defaults(), ],
        ]);

        $email = $request->email;
        $user = User::query()->where("email", $email)->first();

        if ($user === null) {
            throw ValidationException::withMessages([
                "email" => [ __("passwords.user"), ],
            ]);
        }

        if (! PasswordResetTokenHelper::verifyBrokerToken($email, $request->token)) {
            throw ValidationException::withMessages([
                "email" => [ __("passwords.token"), ],
            ]);
        }

        $user->forceFill([
            "password" => Hash::make($request->password),
            "remember_token" => Str::random(60),
        ])->save();

        PasswordResetTokenHelper::delete($email);

        event(new PasswordReset($user));

        return response()->json(UserTransformerDto::fromUser($user->fresh()), 200);
    }
}
