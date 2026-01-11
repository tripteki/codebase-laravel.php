<?php

namespace Src\V1\Web\Filament\Imports;

use Src\V1\Api\Acl\Models\Permission;
use Src\V1\Web\Filament\Resources\PermissionResource\Forms\PermissionForm;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;

class PermissionImporter extends Importer
{
    /**
     * @var string|null
     */
    protected static ?string $model = Permission::class;

    /**
     * @return \Filament\Actions\Imports\ImportColumn[]
     */
    public static function getColumns(): array
    {
        return [

            ImportColumn::make("name")->label(__("module.permission.labels.name"))->validationAttribute(__("module.permission.labels.name"))->requiredMapping()->rules(
                PermissionForm::validation("name")),
            ImportColumn::make("guard_name")->label(__("module.permission.labels.guard_name"))->validationAttribute(__("module.permission.labels.guard_name"))->requiredMapping()->rules(
                PermissionForm::validation("guard_name")),
        ];
    }

    /**
     * @return \Src\V1\Api\Acl\Models\Permission|null
     */
    public function resolveRecord(): ?Permission
    {
        return Permission::firstOrNew([

            "name" => $this->data["name"],
            "guard_name" => $this->data["guard_name"],
        ]);
    }

    /**
     * @param \Filament\Actions\Imports\Models\Import $import
     * @return string
     */
    public static function getCompletedNotificationBody(Import $import): string
    {
        $successfulRows = $import->successful_rows;
        $body = __("notifications.import.completed", [
            "successfulRows" => number_format($successfulRows),
        ]);

        // If there are failed rows, append the failure message.
        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= " " . __("notifications.import.failed", [
                "failedRows" => number_format($failedRowsCount),
            ]);
        }

        return $body;
    }
}
