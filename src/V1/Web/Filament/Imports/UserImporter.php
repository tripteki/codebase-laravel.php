<?php

namespace Src\V1\Web\Filament\Imports;

use App\Models\User;
use Src\V1\Web\Filament\Resources\UserResource\Forms\UserForm;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;

class UserImporter extends Importer
{
    /**
     * @var string|null
     */
    protected static ?string $model = User::class;

    /**
     * @return \Filament\Actions\Imports\ImportColumn[]
     */
    public static function getColumns(): array
    {
        return [

            ImportColumn::make("name")->label(__("module.user.labels.name"))->validationAttribute(__("module.user.labels.name"))->requiredMapping()->rules(
                UserForm::validation("name")),
            ImportColumn::make("email")->label(__("module.user.labels.email"))->validationAttribute(__("module.user.labels.email"))->requiredMapping()->rules(
                UserForm::validation("email")),
            ImportColumn::make("password")->label(__("module.user.labels.password"))->validationAttribute(__("module.user.labels.password"))->requiredMapping()->rules(
                UserForm::validation("password")),
        ];
    }

    /**
     * @return \App\Models\User|null
     */
    public function resolveRecord(): ?User
    {
        return User::firstOrNew([

            "name" => $this->data["name"],
            "email" => $this->data["email"],
            "password" => $this->data["password"],
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
