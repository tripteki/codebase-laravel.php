<?php

namespace App\Livewire\Admin\User;

use App\Jobs\Admin\User\ProcessUserImport;
use App\Livewire\Base\ImportComponent;

class UserImportComponent extends ImportComponent
{
    /**
     * Get the importer class name.
     *
     * @return string
     */
    protected function getImporterClass(): string
    {
        return \App\Imports\Admin\User\UserImport::class;
    }

    /**
     * Get the process import job class name.
     *
     * @return string
     */
    protected function getProcessImportJobClass(): string
    {
        return ProcessUserImport::class;
    }

    /**
     * Get the view name.
     *
     * @return string
     */
    protected function getViewName(): string
    {
        return "livewire.admin.user.user-import-component";
    }

    /**
     * Get import started message.
     *
     * @return string
     */
    protected function getImportStartedMessage(): string
    {
        return __("module_user.import_started");
    }
}
