<?php

namespace Modules\Auth\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Modules\User\App\Dtos\UserTransformerDto;

class ResetPasswordApiController extends BaseController
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param string $email
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, string $email): JsonResponse
    {
        if (! verify_signed_url(signed_request_frontend_url($request), $request->query("signed"))) {
            abort(403, __("auth.token_invalid"));
        }

        $request->validate([
            "password" => [ "required", "confirmed", Rules\Password::defaults(), ],
        ]);

        $signed = $request->query("signed");

        $resetter = DB::table("password_reset_tokens")
            ->where("email", $email)
            ->where("token", $signed)
            ->first();

        if (! $resetter) {
            throw ValidationException::withMessages([
                "email" => [ __("passwords.token"), ],
            ]);
        }

        $user = User::where("email", $email)->firstOrFail();
        $user->forceFill([
            "password" => Hash::make($request->password),
        ])->save();

        DB::table("password_reset_tokens")->where("email", $email)->delete();

        event(new PasswordReset($user));

        return response()->json(UserTransformerDto::fromUser($user->fresh()), 200);
    }
}
