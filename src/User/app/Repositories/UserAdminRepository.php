<?php

namespace Modules\User\App\Repositories;

use App\Models\Profile;
use App\Models\User;
use App\Repositories\Repository as BaseRepository;
use App\Support\AdminSpreadsheetSupport;
use App\Support\AdminTenancySupport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Acl\App\Models\Role;
use Modules\User\App\Dtos\UserTransformerDto;
use Modules\User\App\Support\AccountNotifier;
use Modules\Log\App\Support\ActivityRecorder;
use Modules\User\App\Exports\UserAdminExport;
use Modules\User\App\Imports\UserAdminImport;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class UserAdminRepository extends BaseRepository
{
    use AdminSpreadsheetSupport {
        importColumnValue as spreadsheetImportColumnValue;
        writerType as spreadsheetWriterType;
        resolveImportTenantId as spreadsheetResolveImportTenantId;
    }

    /**
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function all(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return parent::accessAll(
            fn () => $this->listUsersQuery(),
            sortables: [
                "id",
                "name",
                "email",
                "created_at",
                "updated_at",
                AllowedSort::callback("status", function ($query, bool $descending): void {
                    $direction = $descending ? "desc" : "asc";
                    $query->orderByRaw(
                        "CASE WHEN deleted_at IS NOT NULL THEN 2 WHEN email_verified_at IS NULL THEN 1 ELSE 0 END {$direction}",
                    );
                }),
            ],
            defaultSorts: [ "-created_at", ],
            filterables: [
                AllowedFilter::callback("q", function ($query, $value): void {
                    $this->applyNameEmailSearch($query, $value);
                }),
                AllowedFilter::callback("search", function ($query, $value): void {
                    $this->applyNameEmailSearch($query, $value);
                }),
                AllowedFilter::partial("name"),
                AllowedFilter::partial("email"),
                AllowedFilter::callback("verified", function ($query, $value): void {
                    if ((string) $value === "yes") {
                        $query->whereNotNull("email_verified_at");

                        return;
                    }

                    if ((string) $value === "no") {
                        $query->whereNull("email_verified_at");
                    }
                }),
                AllowedFilter::callback("status", function ($query, $value): void {
                    //
                }),
                AllowedFilter::callback("role", function ($query, $value): void {
                    $role = trim((string) $value);

                    if ($role === "") {
                        return;
                    }

                    $query->whereHas("roles", function ($roles) use ($role): void {
                        $roles->where("name", $role)
                            ->where("guard_name", GuardEnum::WEB->value);
                    });
                }),
                AdminTenancySupport::allowedTenantScope(),
            ],
            defaultFilters: [],
        );
    }

    /**
     * @param string $id
     * @return \App\Models\User|null
     */
    public function get(string $id): ?User
    {
        return parent::accessGet(
            fn () => $this->scopedUsersQuery()->withTrashed()->findOrFail($id),
        );
    }

    /**
     * @param array $userData
     * @param array<string, mixed> $profileData
     * @param string|null $tenantId
     * @param string|null $role
     * @param string|null $avatarPath
     * @return \App\Models\User|null
     */
    public function create(array $userData, array $profileData = [], ?string $tenantId = null, ?string $role = null, ?string $avatarPath = null): ?User
    {
        return parent::mutateCreate(
            function () use ($userData, $profileData, $tenantId, $role, $avatarPath): User {
                $payload = $userData;
                $verifiedAt = now();

                if (is_tenancy()) {
                    if (filled(current_tenant_id())) {
                        $payload["tenant_id"] = current_tenant_id();
                    } elseif (is_central()) {
                        $payload["tenant_id"] = $tenantId;
                    }
                }

                $user = User::create($payload);
                $user->forceFill([ "email_verified_at" => $verifiedAt, ])->save();
                $this->saveProfile($user, $profileData, $avatarPath);
                $this->assignStoredRole($user, $role);
                $user = $user->fresh([ "profile", ]);
                $this->recordUserCreated($user, $profileData, $role);

                return $user;
            },
        );
    }

    /**
     * @param string $id
     * @param array $userData
     * @param array<string, mixed> $profileData
     * @param string|null $role
     * @param string|null $avatarPath
     * @return \App\Models\User|null
     */
    public function update(string $id, array $userData, array $profileData = [], ?string $role = null, ?string $avatarPath = null): ?User
    {
        return parent::mutateUpdate(
            function () use ($id, $userData, $profileData, $role, $avatarPath): User {
                $user = $this->scopedUsersQuery()->findOrFail($id);
                $previousRole = UserTransformerDto::primaryRole($user);
                $user->update($userData);
                $this->saveProfile($user, $profileData, $avatarPath);
                $this->assignStoredRole($user, $role);
                $user = $user->fresh([ "profile", ]);
                $this->recordUserUpdated($user, $userData, $profileData, $role, $previousRole);

                return $user;
            },
        );
    }

    /**
     * @param string $id
     * @return \App\Models\User|null
     */
    public function delete(string $id): ?User
    {
        return parent::mutateDelete(
            function () use ($id): User {
                $user = $this->scopedUsersQuery()->withTrashed()->findOrFail($id);
                $user->delete();
                ActivityRecorder::userDeactivated($user);

                return $user;
            },
        );
    }

    /**
     * @param string $id
     * @return \App\Models\User|null
     */
    public function forceDelete(string $id): ?User
    {
        return parent::mutateDelete(
            function () use ($id): User {
                $user = $this->scopedUsersQuery()->withTrashed()->findOrFail($id);
                $user->forceDelete();
                ActivityRecorder::userForceDeleted($user);

                return $user;
            },
        );
    }

    /**
     * @param string $id
     * @return \App\Models\User|null
     */
    public function restore(string $id): ?User
    {
        return parent::mutateUpdate(
            function () use ($id): User {
                $user = $this->scopedUsersQuery()->withTrashed()->findOrFail($id);
                $user->restore();
                $user = $user->fresh();
                ActivityRecorder::userActivated($user);

                return $user;
            },
        );
    }

    /**
     * @param string $id
     * @return \App\Models\User|null
     */
    public function verify(string $id): ?User
    {
        return parent::mutateUpdate(
            function () use ($id): User {
                $user = $this->scopedUsersQuery()
                    ->whereNull("email_verified_at")
                    ->findOrFail($id);
                $user->forceFill([ "email_verified_at" => now(), ])->save();
                $user = $user->fresh();

                ActivityRecorder::userEmailVerified($user);

                return $user;
            },
        );
    }

    /**
     * @param string $path
     * @return array{imported: int, skipped: int}
     */
    public function importFromFile(string $path): array
    {
        $rows = $this->readAdminImport(new UserAdminImport, $path);
        $this->assertImportColumns($rows);
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

            $name = $this->importColumnValue($normalized, "name");
            $fullName = $this->importColumnValue($normalized, "full_name");
            $displayName = $name ?? $fullName;
            $email = $this->importColumnValue($normalized, "email");
            $password = $this->importColumnValue($normalized, "password");
            $verifiedRaw = $this->importColumnValue($normalized, "email_verified");
            $status = $this->resolveImportStatus(
                $this->importColumnValue($normalized, "status"),
                $verifiedRaw,
            );
            $tenantId = $this->resolveImportTenantIdFromRow($normalized);

            if ($email === null || $password === null || $password === "" || $displayName === null || $displayName === "") {
                $skipped++;

                continue;
            }

            $fullName = $fullName ?? $displayName;

            if ($status === null || $this->importUserExists($email, $tenantId)) {
                $skipped++;

                continue;
            }

            $verified = $status !== "unverified";
            $payload = [
                "name" => $displayName,
                "email" => $email,
                "password" => $password,
            ];

            if (! is_central()) {
                $payload["tenant_id"] = current_tenant_id();
            } elseif ($tenantId !== null) {
                $payload["tenant_id"] = $tenantId;
            }

            try {
                $user = DB::transaction(function () use ($payload, $verified, $fullName, $status): User {
                    $user = User::create($payload);

                    if ($verified) {
                        $user->forceFill([ "email_verified_at" => now(), ])->save();
                    }

                    Profile::query()->create([
                        "user_id" => $user->getKey(),
                        "full_name" => $fullName,
                    ]);

                    $this->assignStoredRole($user, RoleEnum::USER->value);
                    $this->recordUserCreated($user->fresh([ "profile", ]) ?? $user, [
                        "full_name" => $fullName,
                    ], RoleEnum::USER->value);

                    if ($status === "deactivated") {
                        $user->delete();
                    }

                    return $user;
                });
            } catch (\Throwable $exception) {
                report($exception);
                $skipped++;

                continue;
            }

            if ($status === "active") {
                AccountNotifier::accountCreated($user->fresh([ "profile", ]) ?? $user, RoleEnum::USER->value);
            } elseif ($status === "unverified") {
                try {
                    $user->sendEmailVerificationNotification();
                } catch (\Throwable $exception) {
                    report($exception);
                }
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
     * @param array<int, array{field: string, direction: string}> $orders
     * @return string
     */
    public function exportToFile(string $type = "csv", array $filters = [], array $orders = []): string
    {
        $type = in_array($type, [ "csv", "xls", "xlsx", ], true) ? $type : "csv";
        $filename = "users_export_".now()->timestamp.".".$type;
        $relativePath = "export/".$filename;

        Storage::disk("public")->makeDirectory("export");

        $headings = [
            "full_name",
            "email",
            "role",
            "status",
            "created_at",
        ];

        $query = User::query()->with([ "profile", "roles", ]);
        AdminTenancySupport::applyActiveTenantScope($query);
        $this->applyExportFilters($query, $filters);
        $this->applyExportOrders($query, $orders);

        $rows = $query->get()->map(function (User $user): array {
            if ($user->deleted_at !== null) {
                $status = "deactivated";
            } elseif ($user->email_verified_at === null) {
                $status = "unverified";
            } else {
                $status = "active";
            }

            $role = AdminTenancySupport::runWithPermissionsTeam(
                $user->tenant_id !== null ? (string) $user->tenant_id : null,
                fn (): ?string => UserTransformerDto::primaryRole($user),
            );

            return [
                $user->profile?->full_name ?: $user->name,
                $user->email,
                $role,
                $status,
                $user->created_at?->format("Y-m-d H:i:s"),
            ];
        })->all();

        $this->storeAdminExport(
            new UserAdminExport($rows, $headings, __("user.export.sheet_name")),
            $relativePath,
            $type,
        );

        return Storage::disk("public")->path($relativePath);
    }

    /**
     * @param int $months
     * @return array{labels: list<string>, series: list<int>}
     */
    public function registrationTrend(int $months = 12): array
    {
        $labels = [];
        $series = [];

        for ($offset = $months - 1; $offset >= 0; $offset--) {
            $month = Carbon::now()->subMonths($offset)->startOfMonth();
            $labels[] = $month->format("M Y");
            $series[] = $this->scopedUsersQuery()
                ->whereNull("deleted_at")
                ->whereBetween("created_at", [
                    $month->copy()->startOfMonth(),
                    $month->copy()->endOfMonth(),
                ])
                ->count();
        }

        return [
            "labels" => $labels,
            "series" => $series,
        ];
    }

    /**
     * @return array{labels: list<string>, series: list<int>}
     */
    public function usersByRole(): array
    {
        $roles = Role::query()
            ->withCount([
                "users" => function (Builder $query): void {
                    $query->whereNull("deleted_at");
                    AdminTenancySupport::applyActiveTenantScope($query, "users.tenant_id");
                },
            ])
            ->orderByDesc("users_count")
            ->orderBy("name")
            ->get()
            ->filter(static fn (Role $role): bool => $role->users_count > 0)
            ->values();

        return [
            "labels" => $roles->pluck("name")->all(),
            "series" => $roles->pluck("users_count")
                ->map(static fn (mixed $count): int => (int) $count)
                ->values()
                ->all(),
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function scopedUsersQuery(): Builder
    {
        return tap(User::query()->with("profile"), function (Builder $query): void {
            AdminTenancySupport::applyActiveTenantScope($query);
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function listUsersQuery(): Builder
    {
        $query = $this->scopedUsersQuery();
        $filters = AdminTenancySupport::fromRequest(request());
        $status = trim((string) ($filters["status"] ?? ""));

        if ($status === "" && ! array_key_exists("verified", $filters)) {
            $status = "active";
        }

        $this->applyAccountStatus($query, $status);

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return void
     */
    protected function applyAccountStatus(Builder $query, string $status): void
    {
        if ($status === "all") {
            $query->withTrashed();

            return;
        }

        if ($status === "deactivated") {
            $query->onlyTrashed();

            return;
        }

        if ($status === "unverified") {
            $query->whereNull("deleted_at")->whereNull("email_verified_at");

            return;
        }

        if ($status === "") {
            $query->whereNull("deleted_at");

            return;
        }

        if ($status === "active") {
            $query->whereNull("deleted_at")->whereNotNull("email_verified_at");

            return;
        }

        $query->withTrashed();
    }

    /**
     * @param \App\Models\User $user
     * @param string|null $role
     * @return void
     */
    protected function assignStoredRole(User $user, ?string $role): void
    {
        $stored = $this->findStoredRole($role);

        if ($stored === null) {
            return;
        }

        $user->syncRoles([ $stored, ]);
    }

    /**
     * @param string|null $role
     * @return \Modules\Acl\App\Models\Role|null
     */
    protected function findStoredRole(?string $role): ?Role
    {
        $name = trim((string) $role);

        if ($name === "") {
            return null;
        }

        $query = Role::query()
            ->withoutGlobalScopes()
            ->where("name", $name)
            ->where("guard_name", GuardEnum::WEB->value);

        $tenantId = is_tenancy() ? current_tenant_id() : null;

        if ($tenantId === null) {
            $query->where(function ($nested): void {
                $nested->whereNull("tenant_id")->orWhere("tenant_id", "");
            });
        } else {
            $query->where("tenant_id", $tenantId);
        }

        return $query->first();
    }

    /**
     * @param \App\Models\User $user
     * @param array<string, mixed> $profileData
     * @param string|null $role
     * @return void
     */
    protected function recordUserCreated(User $user, array $profileData, ?string $role): void
    {
        ActivityRecorder::replaceLatestProperties($user, "user created", [
            "attributes" => [
                "full_name" => trim((string) ($profileData["full_name"] ?? $user->profile?->full_name ?? $user->name)),
                "email" => (string) $user->email,
                "role" => $role ?? UserTransformerDto::primaryRole($user),
            ],
        ]);
    }

    /**
     * @param \App\Models\User $user
     * @param array<string, mixed> $userData
     * @param array<string, mixed> $profileData
     * @param string|null $role
     * @param string|null $previousRole
     * @return void
     */
    protected function recordUserUpdated(User $user, array $userData, array $profileData, ?string $role, ?string $previousRole): void
    {
        $attributes = [];

        if (array_key_exists("name", $userData) || array_key_exists("full_name", $profileData)) {
            $attributes["full_name"] = trim((string) ($profileData["full_name"] ?? $user->profile?->full_name ?? $user->name));
        }

        if (array_key_exists("email", $userData)) {
            $attributes["email"] = (string) $user->email;
        }

        if ($role !== null && $role !== $previousRole) {
            $attributes["role"] = UserTransformerDto::primaryRole($user) ?? $role;
        }

        if ($attributes === []) {
            return;
        }

        $properties = [ "attributes" => $attributes, ];

        if (! ActivityRecorder::replaceLatestProperties($user, "user updated", $properties)) {
            ActivityRecorder::userUpdated($user, $attributes);
        }
    }

    /**
     * @param \App\Models\User $user
     * @param array<string, mixed> $profileData
     * @param string|null $avatarPath
     * @return void
     */
    protected function saveProfile(User $user, array $profileData, ?string $avatarPath): void
    {
        if ($avatarPath !== null) {
            $profileData["avatar"] = $avatarPath;
        }

        if ($profileData === []) {
            return;
        }

        Profile::query()->updateOrCreate(
            [ "user_id" => $user->getKey(), ],
            $profileData,
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @return void
     */
    protected function applyNameEmailSearch(Builder $query, mixed $value): void
    {
        $term = trim((string) $value);

        if ($term === "") {
            return;
        }

        $query->where(function ($nested) use ($term): void {
            $nested->where("name", "like", "%{$term}%")
                ->orWhere("email", "like", "%{$term}%")
                ->orWhereHas("profile", function ($profile) use ($term): void {
                    $profile->where("full_name", "like", "%{$term}%");
                });
        });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array<int, array{field?: string, direction?: string}> $orders
     * @return void
     */
    protected function applyExportOrders(Builder $query, array $orders): void
    {
        $applied = false;

        foreach ($orders as $order) {
            $field = trim((string) ($order["field"] ?? ""));
            $direction = strtolower(trim((string) ($order["direction"] ?? "asc"))) === "desc" ? "desc" : "asc";

            if ($field === "status") {
                $query->orderByRaw(
                    "CASE WHEN deleted_at IS NOT NULL THEN 2 WHEN email_verified_at IS NULL THEN 1 ELSE 0 END {$direction}",
                );
                $applied = true;

                continue;
            }

            if (! in_array($field, [ "email", "name", "created_at", "updated_at", ], true)) {
                continue;
            }

            $query->orderBy($field, $direction);
            $applied = true;
        }

        if (! $applied) {
            $query->orderBy("created_at", "desc");
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array<string, mixed> $filters
     * @return void
     */
    protected function applyExportFilters(Builder $query, array $filters): void
    {
        $status = trim((string) ($filters["status"] ?? ""));

        if ($status === "" && ! array_key_exists("verified", $filters)) {
            $status = "active";
        }

        $this->applyAccountStatus($query, $status);

        $role = trim((string) ($filters["role"] ?? ""));

        if ($role !== "") {
            $query->whereHas("roles", function ($roles) use ($role): void {
                $roles->where("name", $role)
                    ->where("guard_name", GuardEnum::WEB->value);
            });
        }

        $search = trim((string) ($filters["q"] ?? ""));

        if ($search === "") {
            $search = trim((string) ($filters["search"] ?? ""));
        }

        if ($search !== "") {
            $query->where(function ($nested) use ($search): void {
                $nested->where("name", "like", "%{$search}%")
                    ->orWhere("email", "like", "%{$search}%")
                    ->orWhereHas("profile", function ($profile) use ($search): void {
                        $profile->where("full_name", "like", "%{$search}%");
                    });
            });
        }

        $verified = trim((string) ($filters["verified"] ?? ""));

        if ($verified === "yes") {
            $query->whereNotNull("email_verified_at");
        } elseif ($verified === "no") {
            $query->whereNull("email_verified_at");
        }

        AdminTenancySupport::scopeByTenant($query, $filters["tenant_id"] ?? "");
    }

    /**
     * @param string $email
     * @param string|null $tenantId
     * @return bool
     */
    protected function importUserExists(string $email, ?string $tenantId = null): bool
    {
        $query = User::query();

        if (is_central()) {
            AdminTenancySupport::applyImportTenantScope($query, $tenantId);
        } else {
            AdminTenancySupport::applyActiveTenantScope($query);
        }

        return $query
            ->withTrashed()
            ->where("email", $email)
            ->exists();
    }

    /**
     * @param array<string, mixed> $row
     * @return string|null
     */
    protected function resolveImportTenantIdFromRow(array $row): ?string
    {
        if (! is_central()) {
            return current_tenant_id();
        }

        $raw = $this->importColumnValue($row, "tenant")
            ?? $this->importColumnValue($row, "tenant_id");

        return AdminTenancySupport::resolveTenantIdFromPayload([
            "tenant" => $raw,
            "tenant_id" => $raw,
        ]);
    }

    /**
     * @param array<string, mixed> $row
     * @param string $column
     * @return string|null
     */
    protected function importColumnValue(array $row, string $column): ?string
    {
        $candidates = array_unique(array_filter([
            strtolower($column),
            strtolower((string) __("user.import.column.{$column}")),
            $column === "tenant_id" ? "tenant" : null,
            $column === "tenant" ? "tenant_id" : null,
            $column === "full_name" ? "name" : null,
            $column === "full_name" ? strtolower((string) __("user.import.column.name")) : null,
            $column === "email_verified" ? "email_verified_at" : null,
        ]));

        foreach ($row as $key => $value) {
            $normalizedKey = strtolower(trim((string) $key));

            if (! in_array($normalizedKey, $candidates, true)) {
                continue;
            }

            if ($value === null || $value === "") {
                return null;
            }

            return is_string($value) ? trim($value) : (string) $value;
        }

        return null;
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return void
     */
    protected function assertImportColumns(array $rows): void
    {
        $first = null;

        foreach ($rows as $row) {
            if (is_array($row)) {
                $first = $row;

                break;
            }
        }

        if ($first === null) {
            throw new \RuntimeException(__("user.import.no_data"));
        }

        $keys = array_map(
            static fn (mixed $key): string => strtolower(trim((string) $key)),
            array_keys($first),
        );
        $missing = array_values(array_filter(
            [ "email", "password", ],
            static fn (string $column): bool => ! in_array($column, $keys, true),
        ));

        if ($missing !== []) {
            throw new \RuntimeException(__("user.import.missing_columns", [
                "columns" => implode(", ", $missing),
            ]));
        }

        if (! in_array("name", $keys, true) && ! in_array("full_name", $keys, true)) {
            throw new \RuntimeException(__("user.import.missing_columns", [
                "columns" => "name",
            ]));
        }
    }

    /**
     * @param string|null $status
     * @param string|null $verifiedRaw
     * @return string|null
     */
    protected function resolveImportStatus(?string $status, ?string $verifiedRaw): ?string
    {
        $status = strtolower(trim((string) $status));

        if ($status !== "") {
            return in_array($status, [ "active", "unverified", "deactivated", ], true) ? $status : null;
        }

        return $this->isImportVerified($verifiedRaw) ? "active" : "unverified";
    }

    /**
     * @param string|null $value
     * @return bool
     */
    protected function isImportVerified(?string $value): bool
    {
        if ($value === null || $value === "") {
            return false;
        }

        if (strcasecmp($value, (string) __("user.common.yes")) === 0) {
            return true;
        }

        return $this->isTruthy($value);
    }

    /**
     * @param string|null $value
     * @return bool
     */
    protected function isTruthy(?string $value): bool
    {
        if ($value === null || $value === "") {
            return false;
        }

        return in_array(strtolower($value), [ "1", "true", "yes", "y", ], true);
    }

    /**
     * @param string $type
     * @return string
     */
    protected function writerType(string $type): string
    {
        return match ($type) {
            "xls" => ExcelFormat::XLS,
            "xlsx" => ExcelFormat::XLSX,
            default => ExcelFormat::CSV,
        };
    }
}
