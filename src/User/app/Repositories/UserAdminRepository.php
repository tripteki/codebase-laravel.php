<?php

namespace Modules\User\App\Repositories;

use App\Models\Profile;
use App\Models\User;
use App\Repositories\Repository as BaseRepository;
use App\Support\AdminSpreadsheetSupport;
use App\Support\AdminTenancySupport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Modules\Acl\App\Models\Role;
use Modules\Log\App\Support\ActivityRecorder;
use Modules\User\App\Exports\UserAdminExport;
use Modules\User\App\Imports\UserAdminImport;
use Spatie\QueryBuilder\AllowedFilter;

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
            fn () => $this->scopedUsersQuery(),
            sortables: [ "id", "name", "email", "created_at", "updated_at", ],
            defaultSorts: [ "-created_at", ],
            filterables: [
                AllowedFilter::callback("q", function ($query, $value): void {
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
                    if ((string) $value === "all") {
                        $query->withTrashed();

                        return;
                    }

                    if ((string) $value === "inactive") {
                        $query->onlyTrashed();

                        return;
                    }

                    if ((string) $value === "active") {
                        $query->whereNull("deleted_at");
                    }
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
            fn () => $this->scopedUsersQuery()->findOrFail($id),
        );
    }

    /**
     * @param array $userData
     * @param array<string, mixed> $profileData
     * @param string|null $tenantId
     * @return \App\Models\User|null
     */
    public function create(array $userData, array $profileData = [], ?string $tenantId = null): ?User
    {
        return parent::mutateCreate(
            function () use ($userData, $profileData, $tenantId): User {
                $payload = array_merge($userData, [
                    "email_verified_at" => now(),
                ]);

                if (filled(current_tenant_id())) {
                    $payload["tenant_id"] = current_tenant_id();
                } elseif (is_central()) {
                    $payload["tenant_id"] = $tenantId;
                }

                $user = User::create($payload);

                if (filled($profileData["full_name"] ?? null)) {
                    Profile::query()->create(array_merge($profileData, [
                        "user_id" => $user->getKey(),
                    ]));
                }

                return $user->fresh([ "profile", ]);
            },
        );
    }

    /**
     * @param string $id
     * @param array $userData
     * @param array<string, mixed> $profileData
     * @return \App\Models\User|null
     */
    public function update(string $id, array $userData, array $profileData = []): ?User
    {
        return parent::mutateUpdate(
            function () use ($id, $userData, $profileData): User {
                $user = $this->scopedUsersQuery()->findOrFail($id);
                $user->update($userData);

                if ($profileData !== []) {
                    Profile::query()->updateOrCreate(
                        [ "user_id" => $user->getKey(), ],
                        $profileData,
                    );
                }

                return $user->fresh([ "profile", ]);
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

                return $user->fresh();
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
            $fullName = $this->importColumnValue($normalized, "full_name") ?? $name;
            $email = $this->importColumnValue($normalized, "email");
            $password = $this->importColumnValue($normalized, "password") ?: "12345678";
            $verifiedRaw = $this->importColumnValue($normalized, "email_verified");
            $tenantId = $this->resolveImportTenantIdFromRow($normalized);

            if ($email === null) {
                $skipped++;

                continue;
            }

            if ($name === null) {
                $name = strstr($email, "@", true) ?: $email;
            }

            if ($fullName === null) {
                $skipped++;

                continue;
            }

            if ($this->importUserExists($email, $name, $tenantId)) {
                $skipped++;

                continue;
            }

            $payload = [
                "name" => $name,
                "email" => $email,
                "password" => $password,
                "email_verified_at" => $this->isImportVerified($verifiedRaw) ? now() : null,
            ];

            if (! is_central()) {
                $payload["tenant_id"] = current_tenant_id();
            } elseif ($tenantId !== null) {
                $payload["tenant_id"] = $tenantId;
            }

            $user = User::create($payload);

            Profile::query()->create([
                "user_id" => $user->getKey(),
                "full_name" => $fullName,
            ]);

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
        $type = in_array($type, [ "csv", "xls", "xlsx", ], true) ? $type : "csv";
        $filename = "users_export_".now()->timestamp.".".$type;
        $relativePath = "exports/".$filename;

        Storage::disk("public")->makeDirectory("exports");

        $headings = [
            __("user.export.column.id"),
            __("user.export.column.tenant"),
            __("user.export.column.name"),
            __("user.export.column.full_name"),
            __("user.export.column.email"),
            __("user.export.column.email_verified"),
            __("user.export.column.created_at"),
            __("user.export.column.updated_at"),
        ];

        $query = User::query()->with("profile");
        AdminTenancySupport::applyActiveTenantScope($query);
        $this->applyExportFilters($query, $filters);

        $rows = $query->orderBy("created_at")->get()->map(fn (User $user): array => [
            (string) $user->getKey(),
            AdminTenancySupport::formatExportTenantId($user->tenant_id),
            $user->name,
            $user->profile?->full_name,
            $user->email,
            $user->email_verified_at ? __("user.common.yes") : __("user.common.no"),
            $user->created_at?->toIso8601String(),
            $user->updated_at?->toIso8601String(),
        ])->all();

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
     * @return Builder
     */
    protected function scopedUsersQuery(): Builder
    {
        return tap(User::query()->with("profile"), function (Builder $query): void {
            AdminTenancySupport::applyActiveTenantScope($query);

            $status = trim((string) (AdminTenancySupport::fromRequest(request())["status"] ?? ""));

            if ($status === "all") {
                $query->withTrashed();
            }
        });
    }

    /**
     * @param Builder $query
     * @param array<string, mixed> $filters
     * @return void
     */
    protected function applyExportFilters(Builder $query, array $filters): void
    {
        $status = trim((string) ($filters["status"] ?? ""));

        if ($status === "all") {
            $query->withTrashed();
        } elseif ($status === "inactive") {
            $query->onlyTrashed();
        } elseif ($status === "active" || $status === "") {
            $query->whereNull("deleted_at");
        }

        $search = trim((string) ($filters["q"] ?? ""));

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
     * @param string $name
     * @param ?string $tenantId
     * @return bool
     */
    protected function importUserExists(string $email, string $name, ?string $tenantId = null): bool
    {
        $query = User::query();

        if (is_central()) {
            AdminTenancySupport::applyImportTenantScope($query, $tenantId);
        } else {
            AdminTenancySupport::applyActiveTenantScope($query);
        }

        return $query
            ->where(function ($nested) use ($email, $name): void {
                $nested->where("email", $email)
                    ->orWhere("name", $name);
            })
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
