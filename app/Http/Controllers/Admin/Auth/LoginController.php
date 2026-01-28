<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController
{
    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        return view("livewire.admin.auth.login");
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            "identifier" => ["required", "string"],
            "password" => ["required", "string"],
        ]);

        $field = filter_var($request->identifier, FILTER_VALIDATE_EMAIL) ? "email" : "name";

        if (
            ! Auth::guard("web")->attempt(
                [
                    $field => $request->identifier,
                    "password" => $request->password,
                ],
                $request->boolean("remember")
            )
        ) {
            throw ValidationException::withMessages([
                "identifier" => __("auth.failed"),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended("/admin");
    }

    /**
     * Destroy an authenticated session.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard("web")->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect("/admin/login");
    }
}
