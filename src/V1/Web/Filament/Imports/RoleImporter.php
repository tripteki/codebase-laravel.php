<?php

namespace Src\V1\Web\Filament\Imports;

use Src\V1\Api\Acl\Models\Role;
use Src\V1\Web\Filament\Resources\RoleResource\Forms\RoleForm;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;

class RoleImporter extends Importer
{
    /**
     * @var string|null
     */
    protected static ?string $model = Role::class;

    /**
     * @return \Filament\Actions\Imports\ImportColumn[]
     */
    public static function getColumns(): array
    {
        return [

            ImportColumn::make("name")->label(__("module.role.labels.name"))->validationAttribute(__("module.role.labels.name"))->requiredMapping()->rules(
                RoleForm::validation("name")),
            ImportColumn::make("guard_name")->label(__("module.role.labels.guard_name"))->validationAttribute(__("module.role.labels.guard_name"))->requiredMapping()->rules(
                RoleForm::validation("guard_name")),
        ];
    }

    /**
     * @return \Src\V1\Api\Acl\Models\Role|null
     */
    public function resolveRecord(): ?Role
    {
        return Role::firstOrNew([

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
