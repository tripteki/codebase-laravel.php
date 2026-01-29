<?php

namespace App\Livewire\Admin\User;

use App\Jobs\Admin\User\ProcessUserExport;
use App\Livewire\Base\ExportComponent;
use App\Models\User;

class UserExportComponent extends ExportComponent
{
    /**
     * Get the exporter class name.
     *
     * @return string
     */
    protected function getExporterClass(): string
    {
        return \App\Exports\Admin\User\UserExport::class;
    }

    /**
     * Get the process export job class name.
     *
     * @return string
     */
    protected function getProcessExportJobClass(): string
    {
        return ProcessUserExport::class;
    }

    /**
     * Get the view name.
     *
     * @return string
     */
    protected function getViewName(): string
    {
        return "livewire.admin.user.user-export-component";
    }

    /**
     * Get total rows count for export.
     *
     * @return int
     */
    protected function getTotalRowsCount(): int
    {
        return User::query()->count();
    }

    /**
     * Get export started message.
     *
     * @return string
     */
    protected function getExportStartedMessage(): string
    {
        return __("module_user.export_started");
    }
}
