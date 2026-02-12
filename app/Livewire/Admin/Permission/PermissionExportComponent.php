<?php

namespace App\Livewire\Admin\Permission;

use App\Jobs\Admin\Permission\ProcessPermissionExport;
use App\Livewire\Base\ExportComponent;
use Src\V1\Api\Acl\Enums\PermissionEnum;
use Src\V1\Api\Acl\Models\Permission;

class PermissionExportComponent extends ExportComponent
{
    /**
     * Mount the component.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(PermissionEnum::PERMISSION_EXPORT->value);
    }
    /**
     * Get the exporter class name.
     *
     * @return string
     */
    protected function getExporterClass(): string
    {
        return \App\Exports\Admin\Permission\PermissionExport::class;
    }

    /**
     * Get the process export job class name.
     *
     * @return string
     */
    protected function getProcessExportJobClass(): string
    {
        return ProcessPermissionExport::class;
    }

    /**
     * Get the view name.
     *
     * @return string
     */
    protected function getViewName(): string
    {
        return "livewire.admin.permission.permission-export-component";
    }

    /**
     * Get total rows count for export.
     *
     * @return int
     */
    protected function getTotalRowsCount(): int
    {
        return Permission::query()->count();
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
        return PermissionEnum::PERMISSION_EXPORT->value;
    }
}
