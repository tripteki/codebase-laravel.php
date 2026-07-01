<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Modules\Acl\App\Models\Permission;
use Modules\Acl\App\Models\Role;
use Spatie\QueryBuilder\AllowedFilter;

class AdminTenancySupport
{
    /**
     * @var string
     */
    public const CENTRAL = "central";

    /**
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public static function fromRequest(Request $request): array
    {
        $params = $request->input("filter", []);

        if (is_array($params) && $params !== []) {
            return $params;
        }

        $filters = [];

        foreach (QueryParser::parseFilters((string) $request->query("filters")) as $filter) {
            $filters[$filter["field"]] = $filter["search"];
        }

        return $filters;
    }

    /**
     * @param string $column
     * @return \Spatie\QueryBuilder\AllowedFilter
     */
    public static function allowedTenantScope(string $column = "tenant_id"): AllowedFilter
    {
        return AllowedFilter::callback("tenant_id", function ($query, $value) use ($column): void {
            self::scopeByTenant($query, $value, $column);
        });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $column
     * @return void
     */
    public static function applyActiveTenantScope(Builder $query, string $column = "tenant_id"): void
    {
        $tenantId = current_tenant_id();

        if ($tenantId === null) {
            return;
        }

        $query->where($column, $tenantId);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @param string $column
     * @return void
     */
    public static function scopeByTenant(Builder $query, mixed $value, string $column = "tenant_id"): void
    {
        if (! is_central()) {
            return;
        }

        $value = trim((string) $value);

        if ($value === "") {
            return;
        }

        if ($value === self::CENTRAL) {
            $query->whereNull($column);

            return;
        }

        $query->where($column, $value);
    }

    /**
     * @param array<string, mixed>|null $payload
     * @return bool
     */
    public static function payloadHasTenant(?array $payload): bool
    {
        if (! is_array($payload)) {
            return false;
        }

        return array_key_exists("tenant", $payload) || array_key_exists("tenant_id", $payload);
    }

    /**
     * @param array<string, mixed>|null $payload
     * @return string|null
     */
    public static function resolveTenantIdFromPayload(?array $payload): ?string
    {
        if (! is_array($payload)) {
            return null;
        }

        $raw = $payload["tenant"] ?? $payload["tenant_id"] ?? null;

        if (! is_string($raw)) {
            return null;
        }

        $raw = trim($raw);

        if ($raw === "" || $raw === self::CENTRAL) {
            return null;
        }

        return $raw;
    }

    /**
     * @param array<string, mixed>|null $payload
     * @return string|null
     */
    public static function resolveTenantIdForValidation(?array $payload = null): ?string
    {
        if (self::payloadHasTenant($payload)) {
            return self::resolveTenantIdFromPayload($payload);
        }

        $current = current_tenant_id();

        if ($current !== null) {
            return $current;
        }

        return null;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $tenantId
     * @param string $column
     * @return void
     */
    public static function applyImportTenantScope(Builder $query, ?string $tenantId, string $column = "tenant_id"): void
    {
        if ($tenantId !== null) {
            $query->where($column, $tenantId);

            return;
        }

        $query->whereNull($column);
    }

    /**
     * @param string $name
     * @param string $guard
     * @return \Modules\Acl\App\Models\Role|null
     */
    public static function currentTenantRole(string $name, string $guard): ?Role
    {
        $query = Role::query()
            ->where("name", $name)
            ->where("guard_name", $guard);

        self::applyImportTenantScope($query, current_tenant_id());

        return $query->first();
    }

    /**
     * @param list<string> $names
     * @param string $guard
     * @return \Illuminate\Database\Eloquent\Collection<int, \Modules\Acl\App\Models\Permission>
     */
    public static function currentTenantPermissions(array $names, string $guard): Collection
    {
        $query = Permission::query()
            ->where("guard_name", $guard)
            ->whereIn("name", $names);

        self::applyImportTenantScope($query, current_tenant_id());

        return $query->get();
    }

    /**
     * @param string|null $tenantId
     * @return string
     */
    public static function formatExportTenantId(?string $tenantId): string
    {
        return $tenantId ?? self::CENTRAL;
    }

    /**
     * @param string|null $tenantId
     * @param callable $callback
     * @return mixed
     */
    public static function runWithPermissionsTeam(?string $tenantId, callable $callback): mixed
    {
        if (! function_exists("setPermissionsTeamId") || ! function_exists("getPermissionsTeamId")) {
            return $callback();
        }

        $previous = getPermissionsTeamId();
        setPermissionsTeamId($tenantId ?? "");

        try {
            return $callback();
        } finally {
            setPermissionsTeamId($previous);
        }
    }
}
