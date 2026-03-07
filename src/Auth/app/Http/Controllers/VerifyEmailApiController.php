<?php

namespace Modules\Auth\App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\User\App\Dtos\UserTransformerDto;

class VerifyEmailApiController extends BaseController
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param string $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, string $email): JsonResponse
    {
        if (! verify_signed_url(signed_request_frontend_url($request), $request->query("signed"))) {
            abort(403, __("auth.token_invalid"));
        }

        $user = User::where("email", $email)->firstOrFail();

        if ($user->hasVerifiedEmail()) {
            return response()->json(UserTransformerDto::fromUser($user), 200);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(UserTransformerDto::fromUser($user->fresh()), 200);
    }
}
