<?php

namespace Modules\Auth\App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller as BaseController;

class VerifyEmailController extends BaseController
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @param string $hash
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request, string $id, string $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(
                frontend_url()
            )->with("status", __("auth.verified"));
        }

        $expectedHash = sha1($user->getEmailForVerification());

        if (! hash_equals($expectedHash, $hash)) {
            abort(403, __("auth.token_invalid"));
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->intended(
            frontend_url()
        )->with("status", __("auth.verified"));
    }
}
