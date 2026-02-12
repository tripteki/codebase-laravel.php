<?php

namespace App\Livewire\Admin\Role;

use App\Jobs\Admin\Role\ProcessRoleExport;
use App\Livewire\Base\ExportComponent;
use Src\V1\Api\Acl\Enums\PermissionEnum;
use Src\V1\Api\Acl\Models\Role;

class RoleExportComponent extends ExportComponent
{
    /**
     * Mount the component.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(PermissionEnum::ROLE_EXPORT->value);
    }
    /**
     * Get the exporter class name.
     *
     * @return string
     */
    protected function getExporterClass(): string
    {
        return \App\Exports\Admin\Role\RoleExport::class;
    }

    /**
     * Get the process export job class name.
     *
     * @return string
     */
    protected function getProcessExportJobClass(): string
    {
        return ProcessRoleExport::class;
    }

    /**
     * Get the view name.
     *
     * @return string
     */
    protected function getViewName(): string
    {
        return "livewire.admin.role.role-export-component";
    }

    /**
     * Get total rows count for export.
     *
     * @return int
     */
    protected function getTotalRowsCount(): int
    {
        return Role::query()->count();
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
        return PermissionEnum::ROLE_EXPORT->value;
    }
}
