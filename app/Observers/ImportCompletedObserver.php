<?php

namespace App\Observers;

use Filament\Actions\Imports\Models\Import;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\DatabaseNotification as FilamentDatabaseNotification;
use Filament\Notifications\Notification;

class ImportCompletedObserver
{
    /**
     * @param \Filament\Actions\Imports\Models\Import $import
     * @return void
     */
    public function updated(Import $import): void
    {
        if (! $import->wasChanged('completed_at')) {
            return;
        }

        if (! $import->completed_at) {
            return;
        }

        $user = $import->user;

        if (! $user) {
            return;
        }

        $moduleName = $this->getModuleName($import->importer);

        if (! $moduleName) {
            return;
        }

        if (! $this->hasRequiredTranslations($moduleName)) {
            \Log::warning("ImportCompletedObserver: Missing translations for module: {$moduleName}");
            return;
        }

        $filament = Notification::make()
            ->title(__("module.{$moduleName}.messages.import_completed"))
            ->body(__("module.{$moduleName}.messages.import_body_with_stats", [
                'successful' => $import->successful_rows ?? 0,
                'failed' => $import->failed_rows ?? 0,
            ]))
            ->success()
            ->actions([
                Action::make('mark_read')
                    ->button()
                    ->label(__("module.{$moduleName}.labels.mark_as_read"))
                    ->markAsRead()
                    ->close(),
            ]);

        $payload = $filament->getDatabaseMessage();
        $payload['refresh_datatables'] = true;
        $payload['presentation_icon'] = 'import';
        $payload['failed_rows'] = (int) ($import->failed_rows ?? 0);
        $payload['successful_rows'] = (int) ($import->successful_rows ?? 0);

        $user->notify(new FilamentDatabaseNotification($payload));
    }

    /**
     * @param string $importerClass
     * @return string|null
     */
    private function getModuleName(string $importerClass): ?string
    {
        if (preg_match('/\\\\Imports\\\\(\w+)Importer/', $importerClass, $matches)) {
            return strtolower($matches[1]);
        }

        \Log::warning("ImportCompletedObserver: Unable to determine module name from importer class: {$importerClass}");

        return null;
    }

    /**
     * @param string $moduleName
     * @return bool
     */
    private function hasRequiredTranslations(string $moduleName): bool
    {
        $requiredKeys = [
            "module.{$moduleName}.messages.import_completed",
            "module.{$moduleName}.messages.import_body_with_stats",
            "module.{$moduleName}.labels.mark_as_read",
        ];

        foreach ($requiredKeys as $key) {
            if (! \Lang::has($key)) {
                return false;
            }
        }

        return true;
    }
}
