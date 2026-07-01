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
use Modules\Event\App\Support\AuthAddOnsHelper;
use Modules\User\App\Dtos\UserTransformerDto;
use Modules\User\App\Support\SecurityNotifier;
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
            "token" => [ "required", "string", ],
            "email" => [ "required", "string", "email:rfc,filter", ],
            "password" => [ "required", "string", "min:8", Rules\Password::defaults(), ],
            "password_confirmation" => [ "bail", "required", "string", "same:password", ],
            "tenant" => [ "nullable", "string", ],
        ], [
            "password_confirmation.required" => __("user.auth.password_confirmation_required"),
            "password_confirmation.same" => __("user.auth.password_mismatch"),
        ]);

        $email = $request->email;
        $tenantId = PasswordResetTokenHelper::normalizeTenantId($request->input("tenant"));

        $userQuery = User::query()
            ->withoutTenancy()
            ->where("email", $email);

        if ($tenantId !== null) {
            $userQuery->where("tenant_id", $tenantId);
        } else {
            $userQuery->whereNull("tenant_id");
        }

        $user = $userQuery->first();

        if ($user === null) {
            throw ValidationException::withMessages([
                "email" => [ __("passwords.user"), ],
            ]);
        }

        AuthAddOnsHelper::initializeForUser($user);
        AuthAddOnsHelper::abortIfPasswordResetDisabled();

        if (! PasswordResetTokenHelper::verifyHashedToken($email, $request->token, $tenantId)) {
            throw ValidationException::withMessages([
                "email" => [ __("passwords.token"), ],
            ]);
        }

        $user->forceFill([
            "password" => Hash::make($request->password),
            "remember_token" => Str::random(60),
        ])->save();

        PasswordResetTokenHelper::delete($email, $tenantId);

        SecurityNotifier::passwordChanged($user->fresh() ?? $user);

        event(new PasswordReset($user));

        return response()->json(UserTransformerDto::fromUser($user->fresh()), 200);
    }
}
