<?php

namespace App\Livewire\Admin\Event;

use App\Imports\Admin\Event\EventImport;
use App\Jobs\Admin\Event\ProcessEventImport;
use App\Livewire\Base\ImportComponent;
use App\Enum\Tenant\PermissionEnum;

class EventImportComponent extends ImportComponent
{
    /**
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(PermissionEnum::EVENT_IMPORT->value);
    }

    /**
     * @return string
     */
    protected function getImporterClass(): string
    {
        return EventImport::class;
    }

    /**
     * @return string
     */
    protected function getProcessImportJobClass(): string
    {
        return ProcessEventImport::class;
    }

    /**
     * @return string
     */
    protected function getViewName(): string
    {
        return "livewire.admin.event.event-import-component";
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
        return PermissionEnum::EVENT_IMPORT->value;
    }
}
