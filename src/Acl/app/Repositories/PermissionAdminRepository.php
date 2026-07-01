<?php

namespace Modules\Acl\App\Repositories;

use App\Exports\AdminArrayExport;
use App\Imports\AdminArrayImport;
use App\Repositories\Repository as BaseRepository;
use App\Support\AdminSpreadsheetSupport;
use App\Support\AdminTenancySupport;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Modules\Acl\App\Concerns\ScopesAclByTenant;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Models\Permission;
use Modules\Acl\App\Support\AclGuard;
use Spatie\QueryBuilder\AllowedFilter;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PermissionAdminRepository extends BaseRepository
{
    use AdminSpreadsheetSupport,
        ScopesAclByTenant;

    /**
     * @var int
     */
    protected int $maxLimitPage = 500;

    /**
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        return parent::accessAll(
            fn () => $this->scopedPermissionsQuery(),
            sortables: [ "id", "name", "guard_name", "created_at", "updated_at", ],
            defaultSorts: [ "name", ],
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
     * @return \Modules\Acl\App\Models\Permission
     */
    public function get(string $id): Permission
    {
        return parent::accessGet(
            fn () => $this->scopedPermissionsQuery()->findOrFail($id),
        );
    }

    /**
     * @param array<string, mixed> $permissionData
     * @return \Modules\Acl\App\Models\Permission
     */
    public function create(array $permissionData): Permission
    {
        AclGuard::ensurePermissionNameAvailable(
            (string) ($permissionData["name"] ?? ""),
            (string) ($permissionData["guard_name"] ?? ""),
            null,
            AclGuard::activeTeamId(),
        );

        return parent::mutateCreate(
            fn () => Permission::create($permissionData),
        );
    }

    /**
     * @param string $id
     * @param array<string, mixed> $permissionData
     * @return \Modules\Acl\App\Models\Permission
     */
    public function update(string $id, array $permissionData): Permission
    {
        $permission = $this->scopedPermissionsQuery()->findOrFail($id);
        AclGuard::ensurePermissionIsMutable($permission);

        $nextName = (string) ($permissionData["name"] ?? $permission->name);
        $nextGuard = (string) ($permissionData["guard_name"] ?? $permission->guard_name);

        if ($nextName !== (string) $permission->name || $nextGuard !== (string) $permission->guard_name) {
            AclGuard::ensurePermissionNameAvailable(
                $nextName,
                $nextGuard,
                (string) $permission->getKey(),
                $permission->tenant_id !== null ? (string) $permission->tenant_id : null,
            );
        }

        return parent::mutateUpdate(function () use ($permission, $permissionData): Permission {
            $permission->update($permissionData);

            return $permission->fresh();
        });
    }

    /**
     * @param string $id
     * @return \Modules\Acl\App\Models\Permission
     */
    public function delete(string $id): Permission
    {
        $permission = $this->scopedPermissionsQuery()->findOrFail($id);
        AclGuard::ensurePermissionIsMutable($permission);

        return parent::mutateDelete(function () use ($permission): Permission {
            $permission->delete();

            return $permission;
        });
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
            "acl.permission.import.no_data",
            "acl.permission.import.missing_columns",
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

            $name = $this->importColumnValue($normalized, "name", "acl.permission.import.column.name");
            $guardName = strtolower((string) (
                $this->importColumnValue($normalized, "guard_name", "acl.permission.import.column.guard_name")
                ?? GuardEnum::WEB->value
            ));
            $tenantId = $this->resolveImportTenantId($normalized, "acl.permission.import.column.tenant");

            if ($name === null || ! in_array($guardName, array_column(GuardEnum::cases(), "value"), true)) {
                $skipped++;

                continue;
            }

            if (AclGuard::isProtectedPermissionName($name)) {
                $skipped++;

                continue;
            }

            $existsQuery = Permission::query()
                ->where("name", $name)
                ->where("guard_name", $guardName);
            AdminTenancySupport::applyImportTenantScope($existsQuery, $tenantId);

            if ($existsQuery->exists()) {
                $skipped++;

                continue;
            }

            $payload = [
                "name" => $name,
                "guard_name" => $guardName,
            ];

            if ($tenantId !== null) {
                $payload["tenant_id"] = $tenantId;
            }

            try {
                $stored = AdminTenancySupport::runWithPermissionsTeam($tenantId, fn (): bool => $this->create($payload) !== null);
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
        $filename = "permissions_export_".now()->timestamp.".".$type;
        $relativePath = "export/".$filename;

        Storage::disk("public")->makeDirectory("export");

        $headings = [
            "name",
            "guard_name",
        ];

        $query = $this->scopedPermissionsQuery();
        $this->applyAclExportFilters($query, $filters);

        $rows = $query
            ->orderBy("name")
            ->get()
            ->map(fn (Permission $permission): array => [
                (string) $permission->name,
                (string) $permission->guard_name,
            ])
            ->all();

        $this->storeAdminExport(
            new AdminArrayExport($rows, $headings, __("acl.permission.export.sheet_name")),
            $relativePath,
            $type,
        );

        return Storage::disk("public")->path($relativePath);
    }
}
