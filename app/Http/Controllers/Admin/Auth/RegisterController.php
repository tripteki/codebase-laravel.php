<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Src\V1\Api\Acl\Enums\GuardEnum;
use Src\V1\Api\Acl\Enums\RoleEnum;
use Src\V1\Api\Acl\Models\Role;

class RegisterController
{
    /**
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        if (is_central()) {
            return redirect()->to(tenant_routes("admin.login"));
        }
        if (! config("tenancy.is_tenancy")) {
            return redirect()->to(tenant_routes("admin.login"));
        }

        return view("livewire.admin.auth.tenant.register");
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        if (is_central()) {
            return redirect()->to(tenant_routes("admin.login"));
        }
        if (! config("tenancy.is_tenancy")) {
            return redirect()->to(tenant_routes("admin.login"));
        }

        $emailUniqueRule = (config("tenancy.is_tenancy") && tenant())
            ? tenant()->unique("users", "email")
            : Rule::unique("users", "email");

        $validated = $request->validate([
            "full_name" => ["nullable", "string", "max:255"],
            "email" => ["required", "string", "email", "max:255", $emailUniqueRule],
            "password" => ["required", "string", "min:8", "confirmed"],
        ], [
            "email.required" => __("validation.required", ["attribute" => __("auth.email_address")]),
            "email.email" => __("validation.email", ["attribute" => __("auth.email_address")]),
            "password.required" => __("validation.required", ["attribute" => __("auth.password")]),
            "password.min" => __("validation.min.string", ["attribute" => __("auth.password"), "min" => 8]),
            "password.confirmed" => __("validation.confirmed", ["attribute" => __("auth.password")]),
        ]);

        $name = Str::before($validated["email"], "@");
        $payload = [
            "name" => $name,
            "email" => $validated["email"],
            "password" => Hash::make($validated["password"]),
        ];
        if (config("tenancy.is_tenancy") && tenancy()->initialized) {
            $payload["tenant_id"] = tenant("id");
        }
        $user = User::query()->create($payload);

        $fullName = isset($validated["full_name"]) ? trim((string) $validated["full_name"]) : "";
        if ($fullName !== "") {
            Profile::query()->create([
                "user_id" => $user->id,
                "full_name" => $fullName,
            ]);
        }

        $role = Role::query()
            ->where("name", RoleEnum::VISITOR->value)
            ->where("guard_name", GuardEnum::WEB->value)
            ->firstOrFail();
        $user->assignRole($role->name);

        event(new Registered($user));

        return redirect()->to(tenant_routes("admin.register"))->with("success", __("auth.registered"));
    }
}
