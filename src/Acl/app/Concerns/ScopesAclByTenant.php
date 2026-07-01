<?php

namespace Modules\Acl\App\Concerns;

use App\Support\AdminTenancySupport;
use Illuminate\Database\Eloquent\Builder;
use Modules\Acl\App\Models\Permission;
use Modules\Acl\App\Models\Role;

trait ScopesAclByTenant
{
    /**
     * @return Builder
     */
    protected function scopedRolesQuery(): Builder
    {
        return tap(Role::query(), static function (Builder $query): void {
            AdminTenancySupport::applyActiveTenantScope($query);
        });
    }

    /**
     * @return Builder
     */
    protected function scopedPermissionsQuery(): Builder
    {
        return tap(Permission::query(), static function (Builder $query): void {
            AdminTenancySupport::applyActiveTenantScope($query);
        });
    }

    /**
     * @param Builder $query
     * @param array<string, mixed> $filters
     * @return void
     */
    protected function applyAclExportFilters(Builder $query, array $filters): void
    {
        $search = trim((string) ($filters["q"] ?? ""));

        if ($search !== "") {
            $query->where("name", "like", "%{$search}%");
        }

        $guardName = trim((string) ($filters["guard_name"] ?? ""));

        if ($guardName !== "") {
            $query->where("guard_name", $guardName);
        }

        AdminTenancySupport::scopeByTenant($query, $filters["tenant_id"] ?? "");
    }
}
