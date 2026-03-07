<?php

namespace Modules\Auth\App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Modules\Auth\App\Events\UserAuthReset;
use Modules\Auth\App\Mail\ResetPasswordMail;
use Modules\User\App\Dtos\UserTransformerDto;
use App\Http\Controllers\Controller as BaseController;

class PasswordResetLinkController extends BaseController
{
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
        $user = User::where("email", $email)->first();

        if ($user) {
            $signedUrl = signed_frontend_url("auth/reset-password/".$email);
            parse_str((string) parse_url($signedUrl, PHP_URL_QUERY), $query);
            $signed = $query["signed"] ?? null;

            if (is_string($signed) && $signed !== "") {
                DB::table("password_reset_tokens")->updateOrInsert(
                    [ "email" => $email, ],
                    [ "token" => $signed, "created_at" => now(), ]
                );

                Mail::to($email)->send(new ResetPasswordMail(
                    userName: $user->name,
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
