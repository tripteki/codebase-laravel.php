<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Helpers\SettingHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController
{
    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (! is_central()) {
            return view("livewire.admin.auth.tenant.login");
        }

        $authTenancyView = trim((string) SettingHelper::get("AUTH_TENANCY", "0")) === "1";

        return $authTenancyView
            ? view("livewire.admin.auth.tenant.login")
            : view("livewire.admin.auth.login");
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
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

        $user = Auth::guard("web")->user();

        if (! $user) {
            Auth::guard("web")->logout();
            throw ValidationException::withMessages([
                "identifier" => __("auth.failed"),
            ]);
        }

        $isCentral = is_central();
        $hasSuperAdminRole = $user->hasRole(\Src\V1\Api\Acl\Enums\RoleEnum::SUPERADMIN->value);

        if ($isCentral && ! $hasSuperAdminRole) {
            Auth::guard("web")->logout();
            throw ValidationException::withMessages([
                "identifier" => __("auth.failed"),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(tenant_routes("home"));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard("web")->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->to(tenant_routes("admin.login"));
    }
}
