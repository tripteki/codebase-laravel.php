<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ResetPasswordController
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create(Request $request)
    {
        if (is_central()) {
            return redirect()->to(tenant_routes("admin.login"));
        }

        return view("livewire.admin.auth.tenant.reset-password", [
            "email" => $request->email,
            "token" => $request->route("token"),
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        if (is_central()) {
            return redirect()->to(tenant_routes("admin.login"));
        }
        $request->validate([
            "token" => ["required", "string"],
            "email" => ["required", "string", "email"],
            "password" => ["required", "confirmed", Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::broker("users_eloquent")->reset(
            $request->only("email", "password", "password_confirmation", "token"),
            function ($user) use ($request) {
                $user->forceFill([
                    "password" => Hash::make($request->password),
                    "remember_token" => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                "email" => [__($status)],
            ]);
        }

        return redirect()->to(tenant_routes("admin.login"))->with("status", __($status));
    }
}
