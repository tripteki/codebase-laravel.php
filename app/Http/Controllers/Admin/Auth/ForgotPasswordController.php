<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ForgotPasswordController
{
    /**
     * Show the forgot password form.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        return view("livewire.admin.auth.forgot-password");
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            "email" => ["required", "email"],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::broker("users_eloquent")->sendResetLink(
            $request->only("email")
        );

        if ($status != Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                "email" => [__($status)],
            ]);
        }

        return back()->with("status", __($status));
    }
}
