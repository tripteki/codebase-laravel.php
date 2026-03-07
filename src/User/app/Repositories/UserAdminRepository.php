<?php

namespace Modules\User\App\Repositories;

use App\Models\User;
use App\Repositories\Repository as BaseRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use Modules\User\App\Exports\UserAdminExport;
use Modules\User\App\Imports\UserAdminImport;

class UserAdminRepository extends BaseRepository
{
    /**
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function all(): \Illuminate\Pagination\LengthAwarePaginator
    {
        $model = User::query();

        return parent::accessAll(
            fn () => $model,
            sortables: [ "id", "name", "email", "created_at", "updated_at", ],
            defaultSorts: [ "-created_at", ],
            filterables: [ "id", "name", "email", "created_at", "updated_at", ],
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
            fn () => User::findOrFail($id)
        );
    }

    /**
     * @param array $userData
     * @return \App\Models\User|null
     */
    public function create(array $userData): ?User
    {
        return parent::mutateCreate(
            fn () => User::create(array_merge($userData, [
                "email_verified_at" => now(),
            ]))
        );
    }

    /**
     * @param string $id
     * @param array $userData
     * @return \App\Models\User|null
     */
    public function update(string $id, array $userData): ?User
    {
        return parent::mutateUpdate(
            function () use ($id, $userData): User {
                $user = User::findOrFail($id);
                $user->update($userData);

                return $user->fresh();
            }
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
                $user = User::findOrFail($id);
                $user->delete();

                return $user;
            }
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
                $user = User::withTrashed()->findOrFail($id);
                $user->restore();

                return $user->fresh();
            }
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
                $user = User::whereNull("email_verified_at")->findOrFail($id);
                $user->forceFill([ "email_verified_at" => now(), ])->save();

                return $user->fresh();
            }
        );
    }

    /**
     * @param string $path
     * @return array{imported: int, skipped: int}
     */
    public function importFromFile(string $path): array
    {
        if (! is_file($path)) {
            throw new ModelNotFoundException("Import file not found.");
        }

        $rows = Excel::toArray(new UserAdminImport(), $path)[0] ?? [];
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
            $email = $this->importColumnValue($normalized, "email");
            $password = $this->importColumnValue($normalized, "password") ?: "12345678";
            $verifiedRaw = $this->importColumnValue($normalized, "email_verified");

            if ($name === null || $email === null) {
                $skipped++;

                continue;
            }

            if (User::where("email", $email)->orWhere("name", $name)->exists()) {
                $skipped++;

                continue;
            }

            User::create([
                "name" => $name,
                "email" => $email,
                "password" => $password,
                "email_verified_at" => $this->isImportVerified($verifiedRaw) ? now() : null,
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
     * @return string
     */
    public function exportToFile(string $type = "csv"): string
    {
        $type = in_array($type, [ "csv", "xls", "xlsx", ], true) ? $type : "csv";
        $directory = storage_path("app/exports");

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = "users_export_".now()->timestamp.".".$type;
        $relativePath = "exports/".$filename;

        $headings = [
            __("user.export.column.id"),
            __("user.export.column.name"),
            __("user.export.column.email"),
            __("user.export.column.email_verified"),
            __("user.export.column.created_at"),
            __("user.export.column.updated_at"),
        ];

        $rows = User::query()->orderBy("created_at")->get()->map(function (User $user): array {
            return [
                (string) $user->getKey(),
                $user->name,
                $user->email,
                $user->email_verified_at ? __("user.common.yes") : __("user.common.no"),
                $user->created_at?->toIso8601String(),
                $user->updated_at?->toIso8601String(),
            ];
        })->all();

        Excel::store(
            new UserAdminExport($rows, $headings, __("user.export.sheet_name")),
            $relativePath,
            "local",
            $this->writerType($type)
        );

        return storage_path("app/".$relativePath);
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
