<?php

namespace Modules\Auth\App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Modules\User\App\Dtos\UserTransformerDto;
use App\Http\Controllers\Controller as BaseController;

class NewPasswordController extends BaseController
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            "token" => [ "required", "string", ],
            "email" => [ "required", "string", "email", ],
            "password" => [ "required", "confirmed", Rules\Password::defaults(), ],
        ]);

        $status = Password::reset(
            $request->only("email", "password", "password_confirmation", "token"),
            function (User $user, string $password): void {
                $user->forceFill([
                    "password" => Hash::make($password),
                    "remember_token" => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                "email" => [ __($status), ],
            ]);
        }

        $user = User::where("email", $request->email)->firstOrFail();

        return response()->json(UserTransformerDto::fromUser($user->fresh()), 200);
    }
}
