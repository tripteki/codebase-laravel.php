<?php

namespace App\Livewire\Admin\Permission;

use App\Jobs\Admin\Permission\ProcessPermissionImport;
use App\Livewire\Base\ImportComponent;
use Src\V1\Api\Acl\Enums\PermissionEnum;

class PermissionImportComponent extends ImportComponent
{
    /**
     * Mount the component.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(PermissionEnum::PERMISSION_IMPORT->value);
    }
    /**
     * Get the importer class name.
     *
     * @return string
     */
    protected function getImporterClass(): string
    {
        return \App\Imports\Admin\Permission\PermissionImport::class;
    }

    /**
     * Get the process import job class name.
     *
     * @return string
     */
    protected function getProcessImportJobClass(): string
    {
        return ProcessPermissionImport::class;
    }

    /**
     * Get the view name.
     *
     * @return string
     */
    protected function getViewName(): string
    {
        return "livewire.admin.permission.permission-import-component";
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
        return PermissionEnum::PERMISSION_IMPORT->value;
    }
}
