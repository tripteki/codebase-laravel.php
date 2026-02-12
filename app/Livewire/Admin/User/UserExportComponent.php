<?php

namespace App\Livewire\Admin\User;

use App\Jobs\Admin\User\ProcessUserExport;
use App\Livewire\Base\ExportComponent;
use App\Models\User;
use Src\V1\Api\User\Enums\PermissionEnum;

class UserExportComponent extends ExportComponent
{
    /**
     * Mount the component.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(PermissionEnum::USER_EXPORT->value);
    }
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
        return __("module_base.export_started");
    }

    /**
     * Get export download permission.
     *
     * @return string
     */
    protected function getExportDownloadPermission(): string
    {
        return PermissionEnum::USER_EXPORT->value;
    }
}
