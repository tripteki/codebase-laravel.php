<?php

namespace App\Livewire\Admin\Role;

use App\Imports\Admin\Role\RoleImport;
use App\Jobs\Admin\Role\ProcessRoleImport;
use App\Livewire\Base\ImportComponent;
use Src\V1\Api\Acl\Enums\PermissionEnum;

class RoleImportComponent extends ImportComponent
{
    /**
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(PermissionEnum::ROLE_IMPORT->value);
    }
    /**
     * @return string
     */
    protected function getImporterClass(): string
    {
        return RoleImport::class;
    }

    /**
     * @return string
     */
    protected function getProcessImportJobClass(): string
    {
        return ProcessRoleImport::class;
    }

    /**
     * @return string
     */
    protected function getViewName(): string
    {
        return "livewire.admin.role.role-import-component";
    }

    /**
     * @return string
     */
    protected function getImportStartedMessage(): string
    {
        return __("module_base.import_started");
    }

    /**
     * @return string
     */
    protected function getImportUploadPermission(): string
    {
        return PermissionEnum::ROLE_IMPORT->value;
    }
}
