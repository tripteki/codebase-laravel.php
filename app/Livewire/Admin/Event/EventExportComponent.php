<?php

namespace App\Livewire\Admin\Event;

use App\Exports\Admin\Event\EventExport;
use App\Jobs\Admin\Event\ProcessEventExport;
use App\Livewire\Base\ExportComponent;
use App\Models\Tenant;
use App\Enum\Tenant\PermissionEnum;

class EventExportComponent extends ExportComponent
{
    /**
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(PermissionEnum::EVENT_EXPORT->value);
    }

    /**
     * @return string
     */
    protected function getExporterClass(): string
    {
        return EventExport::class;
    }

    /**
     * @return string
     */
    protected function getProcessExportJobClass(): string
    {
        return ProcessEventExport::class;
    }

    /**
     * @return string
     */
    protected function getViewName(): string
    {
        return "livewire.admin.event.event-export-component";
    }

    /**
     * @return int
     */
    protected function getTotalRowsCount(): int
    {
        return Tenant::query()->count();
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
        return PermissionEnum::EVENT_EXPORT->value;
    }
}
