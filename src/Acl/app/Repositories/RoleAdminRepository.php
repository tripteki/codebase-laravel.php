<?php

namespace Modules\Acl\App\Repositories;

use App\Exports\AdminArrayExport;
use App\Imports\AdminArrayImport;
use App\Repositories\Repository as BaseRepository;
use App\Support\AdminSpreadsheetSupport;
use App\Support\AdminTenancySupport;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Acl\App\Concerns\ScopesAclByTenant;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Models\Permission;
use Modules\Acl\App\Models\Role;
use Modules\Acl\App\Support\AclGuard;
use Modules\Log\App\Support\ActivityRecorder;
use Spatie\QueryBuilder\AllowedFilter;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RoleAdminRepository extends BaseRepository
{
    use AdminSpreadsheetSupport,
        ScopesAclByTenant;

    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        return parent::accessAll(
            fn () => $this->scopedRolesQuery()->with("permissions"),
            sortables: [ "id", "name", "guard_name", "created_at", "updated_at", ],
            defaultSorts: [ "-created_at", ],
            filterables: [
                AllowedFilter::callback("q", function ($query, $value): void {
                    $term = trim((string) $value);

                    if ($term === "") {
                        return;
                    }

                    $query->where("name", "like", "%{$term}%");
                }),
                AllowedFilter::partial("name"),
                AllowedFilter::exact("guard_name"),
                AdminTenancySupport::allowedTenantScope(),
            ],
            defaultFilters: [],
        );
    }

    /**
     * @param string $id
     * @return Role
     */
    public function get(string $id): Role
    {
        return parent::accessGet(
            fn () => $this->scopedRolesQuery()->with("permissions")->findOrFail($id),
        );
    }

    /**
     * @param array<string, mixed> $roleData
     * @param array<int, string>|null $permissionIds
     * @return Role
     */
    public function create(array $roleData, ?array $permissionIds = null): Role
    {
        if (AclGuard::isProtectedRoleName((string) ($roleData["name"] ?? ""))) {
            throw new HttpException(422, __("acl.role.protected"));
        }

        return parent::mutateCreate(function () use ($roleData, $permissionIds): Role {
            return DB::transaction(function () use ($roleData, $permissionIds): Role {
                $role = Role::create($roleData);
                $this->syncPermissions($role, $permissionIds);

                return $role->fresh([ "permissions", ]);
            });
        });
    }

    /**
     * @param string $id
     * @param array<string, mixed> $roleData
     * @param array<int, string>|null $permissionIds
     * @return Role
     */
    public function update(string $id, array $roleData, ?array $permissionIds = null): Role
    {
        $role = $this->scopedRolesQuery()->with("permissions")->findOrFail($id);
        AclGuard::ensureRoleIsMutable($role);

        if (
            isset($roleData["name"])
            && AclGuard::isProtectedRoleName((string) $roleData["name"])
        ) {
            throw new HttpException(422, __("acl.role.protected"));
        }

        return parent::mutateUpdate(function () use ($role, $roleData, $permissionIds): Role {
            return DB::transaction(function () use ($role, $roleData, $permissionIds): Role {
                $role->update($roleData);
                $this->syncPermissions($role, $permissionIds);

                return $role->fresh([ "permissions", ]);
            });
        });
    }

    /**
     * @param string $id
     * @return Role
     */
    public function delete(string $id): Role
    {
        $role = $this->scopedRolesQuery()->with("permissions")->findOrFail($id);
        AclGuard::ensureRoleIsMutable($role);

        return parent::mutateDelete(function () use ($role): Role {
            $role->delete();

            return $role;
        });
    }

    /**
     * @param Role $role
     * @param array<int, string>|null $permissionIds
     * @return void
     */
    protected function syncPermissions(Role $role, ?array $permissionIds): void
    {
        if ($permissionIds === null) {
            return;
        }

        $previousPermissionNames = $role->permissions->pluck("name")->values()->all();

        $permissions = $this->scopedPermissionsQuery()
            ->whereIn("id", $permissionIds)
            ->where("guard_name", $role->guard_name)
            ->get();

        $role->syncPermissions($permissions);

        $role = $role->fresh([ "permissions", ]);

        ActivityRecorder::rolePermissionsSynced(
            $role,
            $previousPermissionNames,
            $role->permissions->pluck("name")->values()->all(),
        );
    }

    /**
     * @param string $path
     * @return array{imported: int, skipped: int}
     */
    public function importFromFile(string $path): array
    {
        $rows = $this->readAdminImport(new AdminArrayImport, $path);
        $imported = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            if (! is_array($row)) {
                $skipped++;

                continue;
            }

            $normalized = [];

            foreach ($row as $key => $value) {
                $normalized[strtolower(trim((string) $key))] = is_string($value) ? trim($value) : $value;
            }

            $name = $this->importColumnValue($normalized, "name", "acl.role.import.column.name");
            $guardName = $this->importColumnValue($normalized, "guard_name", "acl.role.import.column.guard_name")
                ?? GuardEnum::WEB->value;
            $tenantId = $this->resolveImportTenantId($normalized, "acl.role.import.column.tenant");

            if ($name === null) {
                $skipped++;

                continue;
            }

            if (AclGuard::isProtectedRoleName($name)) {
                $skipped++;

                continue;
            }

            $existsQuery = Role::query()
                ->where("name", $name)
                ->where("guard_name", $guardName);
            AdminTenancySupport::applyImportTenantScope($existsQuery, $tenantId);

            if ($existsQuery->exists()) {
                $skipped++;

                continue;
            }

            $permissionNames = $this->parseImportList(
                $this->importColumnValue($normalized, "permissions", "acl.role.import.column.permissions"),
            );

            $permissionQuery = Permission::query()
                ->where("guard_name", $guardName)
                ->whereIn("name", $permissionNames);
            AdminTenancySupport::applyImportTenantScope($permissionQuery, $tenantId);

            $permissionIds = $permissionQuery
                ->pluck("id")
                ->map(fn ($id) => (string) $id)
                ->all();

            AdminTenancySupport::runWithPermissionsTeam($tenantId, fn () => $this->create(
                [
                    "name" => $name,
                    "guard_name" => $guardName,
                ],
                $permissionIds,
            ));

            $imported++;
        }

        return [
            "imported" => $imported,
            "skipped" => $skipped,
        ];
    }

    /**
     * @param string $type
     * @param array<string, mixed> $filters
     * @return string
     */
    public function exportToFile(string $type = "csv", array $filters = []): string
    {
        $type = in_array($type, ["csv", "xls", "xlsx"], true) ? $type : "csv";
        $filename = "roles_export_".now()->timestamp.".".$type;
        $relativePath = "exports/".$filename;

        Storage::disk("public")->makeDirectory("exports");

        $headings = [
            __("acl.role.export.column.id"),
            __("acl.role.export.column.tenant"),
            __("acl.role.export.column.name"),
            __("acl.role.export.column.guard_name"),
            __("acl.role.export.column.permissions"),
            __("acl.role.export.column.created_at"),
            __("acl.role.export.column.updated_at"),
        ];

        $query = $this->scopedRolesQuery()->with("permissions");
        $this->applyAclExportFilters($query, $filters);

        $rows = $query
            ->orderBy("name")
            ->get()
            ->map(fn (Role $role): array => [
                (string) $role->getKey(),
                AdminTenancySupport::formatExportTenantId($role->tenant_id),
                (string) $role->name,
                (string) $role->guard_name,
                $role->permissions->pluck("name")->implode(", "),
                $role->created_at?->toIso8601String(),
                $role->updated_at?->toIso8601String(),
            ])
            ->all();

        $this->storeAdminExport(
            new AdminArrayExport($rows, $headings, __("acl.role.export.sheet_name")),
            $relativePath,
            $type,
        );

        return Storage::disk("public")->path($relativePath);
    }

    /**
     * @param string|null $value
     * @return list<string>
     */
    protected function parseImportList(?string $value): array
    {
        if ($value === null || trim($value) === "") {
            return [];
        }

        return array_values(array_filter(array_map("trim", explode(",", $value))));
    }
}
