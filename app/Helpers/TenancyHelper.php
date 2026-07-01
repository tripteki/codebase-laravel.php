<?php

use App\Models\User;
use App\Support\AdminTenancySupport;
use Illuminate\Http\Request;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\RoleEnum;

if (! function_exists("has_tenant")) {
    /**
     * @return bool
     */
    function has_tenant(): bool
    {
        return function_exists("tenancy") && tenancy()->initialized;
    }
}

if (! function_exists("is_central")) {
    /**
     * @return bool
     */
    function is_central(): bool
    {
        return ! has_tenant();
    }
}

if (! function_exists("current_tenant_id")) {
    /**
     * @return string|null
     */
    function current_tenant_id(): ?string
    {
        if (! has_tenant()) {
            return null;
        }

        $tenant = tenant();

        return $tenant !== null ? (string) $tenant->getKey() : null;
    }
}

if (! function_exists("admin_api_prefix")) {
    /**
     * @param string|null $tenantSlug
     * @return string
     */
    function admin_api_prefix(?string $tenantSlug = null): string
    {
        if ($tenantSlug !== null && $tenantSlug !== "") {
            return "/api/v1/{$tenantSlug}/admin";
        }

        if (has_tenant()) {
            return "/api/v1/".tenant("id")."/admin";
        }

        return "/api/v1/admin";
    }
}

if (! function_exists("admin_frontend_prefix")) {
    /**
     * @param string|null $tenantSlug
     * @return string
     */
    function admin_frontend_prefix(?string $tenantSlug = null): string
    {
        if ($tenantSlug !== null && $tenantSlug !== "") {
            return "/{$tenantSlug}/admin";
        }

        if (has_tenant()) {
            return "/".tenant("id")."/admin";
        }

        return "/admin";
    }
}

if (! function_exists("resolve_route_tenant_slug")) {
    /**
     * @param Request|null $request
     * @return string|null
     */
    function resolve_route_tenant_slug(?Request $request = null): ?string
    {
        $request ??= request();
        $tenant = $request->route("tenant");

        return is_string($tenant) && $tenant !== "" ? $tenant : null;
    }
}

if (! function_exists("sync_permissions_team_context")) {
    /**
     * @return void
     */
    function sync_permissions_team_context(): void
    {
        if (! function_exists("setPermissionsTeamId")) {
            return;
        }

        setPermissionsTeamId(current_tenant_id() ?? "");
    }
}

if (! function_exists("is_central_superadmin")) {
    /**
     * @param User|null $user
     * @return bool
     */
    function is_central_superadmin(?User $user): bool
    {
        if ($user === null || $user->tenant_id !== null) {
            return false;
        }

        return AdminTenancySupport::runWithPermissionsTeam(null, fn (): bool => $user->hasRole(RoleEnum::SUPERADMIN->value, GuardEnum::WEB->value));
    }
}
