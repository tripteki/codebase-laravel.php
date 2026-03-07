<?php

namespace App\Livewire\Admin\Permission;

use App\Exports\Admin\Permission\PermissionExport;
use App\Jobs\Admin\Permission\ProcessPermissionExport;
use App\Livewire\Base\ExportComponent;
use Src\V1\Api\Acl\Enums\PermissionEnum;
use Src\V1\Api\Acl\Models\Permission;

class PermissionExportComponent extends ExportComponent
{
    /**
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(PermissionEnum::PERMISSION_EXPORT->value);
    }
    /**
     * @return string
     */
    protected function getExporterClass(): string
    {
        return PermissionExport::class;
    }

    /**
     * @return string
     */
    protected function getProcessExportJobClass(): string
    {
        return ProcessPermissionExport::class;
    }

    /**
     * @return string
     */
    protected function getViewName(): string
    {
        return "livewire.admin.permission.permission-export-component";
    }

    /**
     * @return int
     */
    protected function getTotalRowsCount(): int
    {
        return Permission::query()->count();
    }

    /**
     * @return string
     */
    protected function getExportStartedMessage(): string
    {
        return __("module_base.export_started");
    }

    /**
     * @return string
     */
    protected function getExportDownloadPermission(): string
    {
        return PermissionEnum::PERMISSION_EXPORT->value;
    }
}
