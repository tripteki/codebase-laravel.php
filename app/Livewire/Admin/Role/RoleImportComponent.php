<?php

namespace App\Livewire\Admin\Role;

use App\Jobs\Admin\Role\ProcessRoleImport;
use App\Livewire\Base\ImportComponent;
use Src\V1\Api\Acl\Enums\PermissionEnum;

class RoleImportComponent extends ImportComponent
{
    /**
     * Mount the component.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(PermissionEnum::ROLE_IMPORT->value);
    }
    /**
     * Get the importer class name.
     *
     * @return string
     */
    protected function getImporterClass(): string
    {
        return \App\Imports\Admin\Role\RoleImport::class;
    }

    /**
     * Get the process import job class name.
     *
     * @return string
     */
    protected function getProcessImportJobClass(): string
    {
        return ProcessRoleImport::class;
    }

    /**
     * Get the view name.
     *
     * @return string
     */
    protected function getViewName(): string
    {
        return "livewire.admin.role.role-import-component";
    }

    /**
     * Get import started message.
     *
     * @return string
     */
    protected function getImportStartedMessage(): string
    {
        return __("module_base.import_started");
    }

    /**
     * Get import upload permission.
     *
     * @return string
     */
    protected function getImportUploadPermission(): string
    {
        return PermissionEnum::ROLE_IMPORT->value;
    }
}
