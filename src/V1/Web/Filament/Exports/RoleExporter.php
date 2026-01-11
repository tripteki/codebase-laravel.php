<?php

namespace Src\V1\Web\Filament\Exports;

use Src\V1\Api\Acl\Models\Role;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;

class RoleExporter extends Exporter
{
    /**
     * @var string|null
     */
    protected static ?string $model = Role::class;

    /**
     * @return \Filament\Actions\Exports\ExportColumn[]
     */
    public static function getColumns(): array
    {
        return [

            ExportColumn::make("id")->label(__("module.role.labels.id"))->enabledByDefault(true),
            ExportColumn::make("name")->label(__("module.role.labels.name"))->enabledByDefault(true),
            ExportColumn::make("guard_name")->label(__("module.role.labels.guard_name"))->enabledByDefault(true),
            ExportColumn::make("created_at")->label(__("module.role.labels.created_at")),
            ExportColumn::make("updated_at")->label(__("module.role.labels.updated_at")),
        ];
    }

    /**
     * @param \Filament\Actions\Exports\Models\Export $export
     * @return string
     */
    public function getFileName(Export $export): string
    {
        return "roles-{$export->getKey()}";
    }

    /**
     * @param \Filament\Actions\Exports\Models\Export $export
     * @return string
     */
    public static function getCompletedNotificationBody(Export $export): string
    {
        $successfulRows = $export->successful_rows;
        $body = __("notifications.export.completed", [
            "successfulRows" => number_format($successfulRows),
        ]);

        // If there are failed rows, append the failure message.
        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= " " . __("notifications.export.failed", [
                "failedRows" => number_format($failedRowsCount),
            ]);
        }

        return $body;
    }
}
