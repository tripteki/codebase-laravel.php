<?php

namespace Src\V1\Web\Filament\Exports;

use App\Models\User;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;

class UserExporter extends Exporter
{
    /**
     * @var string|null
     */
    protected static ?string $model = User::class;

    /**
     * @return \Filament\Actions\Exports\ExportColumn[]
     */
    public static function getColumns(): array
    {
        return [

            ExportColumn::make("id")->label(__("module.user.labels.id"))->enabledByDefault(true),
            ExportColumn::make("name")->label(__("module.user.labels.name"))->enabledByDefault(true),
            ExportColumn::make("email")->label(__("module.user.labels.email"))->enabledByDefault(true),
            ExportColumn::make("created_at")->label(__("module.user.labels.created_at")),
            ExportColumn::make("updated_at")->label(__("module.user.labels.updated_at")),
            ExportColumn::make("deleted_at")->label(__("module.user.labels.deleted_at")),
        ];
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
