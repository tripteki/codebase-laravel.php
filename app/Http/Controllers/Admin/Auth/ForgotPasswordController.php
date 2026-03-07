<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ForgotPasswordController
{
    /**
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        if (is_central()) {
            return redirect()->to(tenant_routes("admin.login"));
        }

        return view("livewire.admin.auth.tenant.forgot-password");
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
