<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;

trait AdminSpreadsheetSupport
{
    /**
     * @param array<string, mixed> $row
     * @param string $tenantTranslationKey
     * @return string|null
     */
    protected function resolveImportTenantId(array $row, string $tenantTranslationKey): ?string
    {
        if (! is_central()) {
            return current_tenant_id();
        }

        $raw = $this->importColumnValue($row, "tenant", $tenantTranslationKey)
            ?? $this->importColumnValue($row, "tenant_id", $tenantTranslationKey);

        return AdminTenancySupport::resolveTenantIdFromPayload([
            "tenant" => $raw,
            "tenant_id" => $raw,
        ]);
    }

    protected function importColumnValue(array $row, string $column, string $translationKey): ?string
    {
        $candidates = array_unique(array_filter([
            strtolower($column),
            strtolower((string) __($translationKey)),
            $column === "tenant_id" ? "tenant" : null,
            $column === "tenant" ? "tenant_id" : null,
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

    /**
     * @param object $export
     * @param string $relativePath
     * @param string $type
     *
     * @throws \RuntimeException
     */
    protected function storeAdminExport(
        object $export,
        string $relativePath,
        string $type,
    ): void {
        Storage::disk("public")->makeDirectory("exports");

        $exportsDirectory = Storage::disk("public")->path("exports");

        if (! is_writable($exportsDirectory)) {
            throw new \RuntimeException(
                "Export directory is not writable: ".$exportsDirectory,
            );
        }

        try {
            $stored = Excel::store(
                $export,
                $relativePath,
                "public",
                $this->writerType($type),
            );
        } catch (\Throwable $exception) {
            throw new \RuntimeException(
                "Excel export failed: ".$exception->getMessage(),
                0,
                $exception,
            );
        }

        if ($stored !== true) {
            throw new \RuntimeException(
                "Excel export failed: could not write ".$relativePath." to the public disk.",
            );
        }

        if (! Storage::disk("public")->exists($relativePath)) {
            throw new \RuntimeException("Export file was not created.");
        }
    }

    /**
     * @param object $import
     * @param string $path
     * @return list<array<string, mixed>>
     *
     * @throws \RuntimeException
     */
    protected function readAdminImport(object $import, string $path): array
    {
        if (! is_file($path)) {
            throw new \RuntimeException("Import file not found.");
        }

        try {
            $sheets = Excel::toArray($import, $path);
        } catch (\Throwable $exception) {
            throw new \RuntimeException(
                "Excel import failed: ".$exception->getMessage(),
                0,
                $exception,
            );
        }

        $rows = $sheets[0] ?? [];

        return is_array($rows) ? $rows : [];
    }
}
