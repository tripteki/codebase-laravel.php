<?php

namespace App\Observers;

use Filament\Actions\Exports\Models\Export;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

class ExportCompletedObserver
{
    /**
     * Handle the Export "updated" event.
     *
     * @param \Filament\Actions\Exports\Models\Export $export
     * @return void
     */
    public function updated(Export $export): void
    {
        if (! $export->wasChanged('completed_at')) {
            return;
        }

        if (! $export->completed_at) {
            return;
        }

        $user = $export->user;

        if (! $user) {
            return;
        }

        $moduleName = $this->getModuleName($export->exporter);

        if (! $moduleName) {
            return;
        }

        if (! $this->hasRequiredTranslations($moduleName)) {
            \Log::warning("ExportCompletedObserver: Missing translations for module: {$moduleName}");
            return;
        }

        $user->notify(
            Notification::make()
                ->title(__("module.{$moduleName}.messages.export_completed"))
                ->body(__("module.{$moduleName}.messages.export_body_with_download", [
                    'count' => $export->successful_rows ?? 0,
                ]))
                ->success()
                ->actions([
                    Action::make('download_csv')
                        ->button()
                        ->label(__("module.{$moduleName}.labels.download_csv"))
                        ->url(route('filament.exports.download', [
                            'export' => $export->getKey(),
                            'format' => 'csv',
                        ]))
                        ->openUrlInNewTab()
                        ->markAsRead(),
                    Action::make('download_xlsx')
                        ->button()
                        ->color('success')
                        ->label(__("module.{$moduleName}.labels.download_xlsx"))
                        ->url(route('filament.exports.download', [
                            'export' => $export->getKey(),
                            'format' => 'xlsx',
                        ]))
                        ->openUrlInNewTab()
                        ->markAsRead(),
                ])
                ->toDatabase()
        );
    }

    /**
     * Automatically extracts module name from Exporter class.
     *
     * @param string $exporterClass
     * @return string|null
     */
    private function getModuleName(string $exporterClass): ?string
    {
        if (preg_match('/\\\\Exports\\\\(\w+)Exporter/', $exporterClass, $matches)) {
            return strtolower($matches[1]);
        }

        \Log::warning("ExportCompletedObserver: Unable to determine module name from exporter class: {$exporterClass}");

        return null;
    }

    /**
     * Check if module has required translations for export notifications.
     *
     * @param string $moduleName
     * @return bool
     */
    private function hasRequiredTranslations(string $moduleName): bool
    {
        $requiredKeys = [
            "module.{$moduleName}.messages.export_completed",
            "module.{$moduleName}.messages.export_body_with_download",
            "module.{$moduleName}.labels.download_csv",
            "module.{$moduleName}.labels.download_xlsx",
        ];

        foreach ($requiredKeys as $key) {
            if (! \Lang::has($key)) {
                return false;
            }
        }

        return true;
    }
}
