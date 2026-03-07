<?php

namespace App\Livewire\Admin\Role;

use App\Exports\Admin\Role\RoleExport;
use App\Jobs\Admin\Role\ProcessRoleExport;
use App\Livewire\Base\ExportComponent;
use Src\V1\Api\Acl\Enums\PermissionEnum;
use Src\V1\Api\Acl\Models\Role;

class RoleExportComponent extends ExportComponent
{
    /**
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(PermissionEnum::ROLE_EXPORT->value);
    }
    /**
     * @return string
     */
    protected function getExporterClass(): string
    {
        return RoleExport::class;
    }

    /**
     * @return string
     */
    protected function getProcessExportJobClass(): string
    {
        return ProcessRoleExport::class;
    }

    /**
     * @return string
     */
    protected function getViewName(): string
    {
        return "livewire.admin.role.role-export-component";
    }

    /**
     * @return int
     */
    protected function getTotalRowsCount(): int
    {
        return Role::query()->count();
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
        return PermissionEnum::ROLE_EXPORT->value;
    }
}
