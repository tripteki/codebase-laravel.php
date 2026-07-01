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
use Modules\Acl\App\Dtos\RoleTransformerDto;
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
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        return parent::accessAll(
            fn () => $this->withUsersCount($this->scopedRolesQuery()->with("permissions")),
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
     * @return \Modules\Acl\App\Models\Role
     */
    public function get(string $id): Role
    {
        return parent::accessGet(
            fn () => $this->withUsersCount($this->scopedRolesQuery()->with("permissions"))->findOrFail($id),
        );
    }

    /**
     * @param array<string, mixed> $roleData
     * @param array<int, string>|null $permissionIds
     * @return \Modules\Acl\App\Models\Role
     */
    public function create(array $roleData, ?array $permissionIds = null): Role
    {
        if (AclGuard::isProtectedRoleName((string) ($roleData["name"] ?? ""))) {
            throw new HttpException(422, __("acl.role.name_reserved"));
        }

        AclGuard::ensureRoleNameAvailable(
            (string) ($roleData["name"] ?? ""),
            (string) ($roleData["guard_name"] ?? ""),
            null,
            AclGuard::activeTeamId(),
        );

        return parent::mutateCreate(function () use ($roleData, $permissionIds): Role {
            return DB::transaction(function () use ($roleData, $permissionIds): Role {
                $role = Role::create($roleData);
                $this->syncPermissions($role, $permissionIds);
                $role = $role->fresh([ "permissions", ]);
                ActivityRecorder::replaceLatestProperties($role, "role created", [
                    "attributes" => [
                        "name" => (string) $role->name,
                        "guard_name" => (string) $role->guard_name,
                        "permission_ids" => array_values($permissionIds ?? []),
                    ],
                ]);

                return $role;
            });
        });
    }

    /**
     * @param string $id
     * @param array<string, mixed> $roleData
     * @param array<int, string>|null $permissionIds
     * @return \Modules\Acl\App\Models\Role
     */
    public function update(string $id, array $roleData, ?array $permissionIds = null): Role
    {
        $role = $this->scopedRolesQuery()->with("permissions")->findOrFail($id);
        AclGuard::ensureRoleIsMutable($role);

        if (
            isset($roleData["name"])
            && AclGuard::isProtectedRoleName((string) $roleData["name"])
        ) {
            throw new HttpException(422, __("acl.role.name_reserved"));
        }

        $nextName = (string) ($roleData["name"] ?? $role->name);
        $nextGuard = (string) ($roleData["guard_name"] ?? $role->guard_name);

        if ($nextName !== (string) $role->name || $nextGuard !== (string) $role->guard_name) {
            AclGuard::ensureRoleNameAvailable(
                $nextName,
                $nextGuard,
                (string) $role->getKey(),
                $role->tenant_id !== null ? (string) $role->tenant_id : null,
            );
        }

        return parent::mutateUpdate(function () use ($role, $roleData, $permissionIds): Role {
            return DB::transaction(function () use ($role, $roleData, $permissionIds): Role {
                $role->update($roleData);
                $this->syncPermissions($role, $permissionIds);
                $role = $role->fresh([ "permissions", ]);
                $properties = [
                    "attributes" => [
                        "name" => (string) $role->name,
                        "guard_name" => (string) $role->guard_name,
                        "permission_ids" => $permissionIds,
                    ],
                ];

                if (! ActivityRecorder::replaceLatestProperties($role, "role updated", $properties)) {
                    ActivityRecorder::roleUpdated($role, $properties["attributes"]);
                }

                return $role;
            });
        });
    }

    /**
     * @param string $id
     * @return \Modules\Acl\App\Models\Role
     */
    public function delete(string $id): Role
    {
        $role = $this->withUsersCount($this->scopedRolesQuery()->with("permissions"))->findOrFail($id);
        AclGuard::ensureRoleIsMutable($role);

        $usersCount = RoleTransformerDto::usersCount($role);

        if ($usersCount > 0) {
            throw new HttpException(422, __("acl.role.in_use", [ "count" => $usersCount, ]));
        }

        return parent::mutateDelete(function () use ($role): Role {
            $role->delete();

            return $role;
        });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<\Modules\Acl\App\Models\Role> $query
     * @return \Illuminate\Database\Eloquent\Builder<\Modules\Acl\App\Models\Role>
     */
    protected function withUsersCount($query)
    {
        $table = (string) config("permission.table_names.model_has_roles", "model_has_roles");

        return $query->addSelect([
            "users_count" => DB::table($table)
                ->selectRaw("count(*)")
                ->whereColumn("{$table}.role_id", "roles.id"),
        ]);
    }

    /**
     * @param \Modules\Acl\App\Models\Role $role
     * @param array<int, string>|null $permissionIds
     * @return void
     */
    protected function syncPermissions(Role $role, ?array $permissionIds): void
    {
        if ($permissionIds === null) {
            return;
        }

        $permissions = $this->scopedPermissionsQuery()
            ->whereIn("id", $permissionIds)
            ->where("guard_name", $role->guard_name)
            ->get();

        $role->syncPermissions($permissions);
    }

    /**
     * @param string $path
     * @return array{imported: int, skipped: int}
     */
    public function importFromFile(string $path): array
    {
        $rows = $this->readAdminImport(new AdminArrayImport, $path);
        $this->assertSpreadsheetColumns(
            $rows,
            [ "name", "guard_name", ],
            "acl.role.import.no_data",
            "acl.role.import.missing_columns",
        );
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
            $guardName = strtolower((string) (
                $this->importColumnValue($normalized, "guard_name", "acl.role.import.column.guard_name")
                ?? GuardEnum::WEB->value
            ));
            $tenantId = $this->resolveImportTenantId($normalized, "acl.role.import.column.tenant");

            if ($name === null || ! in_array($guardName, array_column(GuardEnum::cases(), "value"), true)) {
                $skipped++;

                continue;
            }

            if (AclGuard::isProtectedRoleName($name)) {
                $skipped++;

                continue;
            }

            $permissionNames = $this->parseImportList(
                $this->importColumnValue($normalized, "permissions", "acl.role.import.column.permissions"),
            );

            try {
                $stored = AdminTenancySupport::runWithPermissionsTeam($tenantId, fn (): bool => DB::transaction(
                    function () use ($name, $guardName, $permissionNames): bool {
                        $permissionIds = $this->scopedPermissionsQuery()
                            ->where("guard_name", $guardName)
                            ->whereIn("name", $permissionNames)
                            ->pluck("id")
                            ->map(fn ($id) => (string) $id)
                            ->all();

                        $existing = $this->scopedRolesQuery()
                            ->where("name", $name)
                            ->where("guard_name", $guardName)
                            ->first();

                        if ($existing !== null) {
                            $existing->load("permissions");
                            $this->syncPermissions($existing, $permissionIds);

                            return true;
                        }

                        return $this->create(
                            [
                                "name" => $name,
                                "guard_name" => $guardName,
                            ],
                            $permissionIds,
                        ) !== null;
                    },
                ));
            } catch (\Throwable $exception) {
                report($exception);
                $stored = false;
            }

            if (! $stored) {
                $skipped++;

                continue;
            }

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
        $relativePath = "export/".$filename;

        Storage::disk("public")->makeDirectory("export");

        $headings = [
            "name",
            "guard_name",
            "permissions",
        ];

        $query = $this->scopedRolesQuery()->with("permissions");
        $this->applyAclExportFilters($query, $filters);

        $rows = $query
            ->orderBy("name")
            ->get()
            ->map(fn (Role $role): array => [
                (string) $role->name,
                (string) $role->guard_name,
                $role->permissions->pluck("name")->implode(", "),
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
