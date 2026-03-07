<?php

namespace App\Livewire\Admin\Stage;

use App\Enum\Event\AddOnEnum;
use App\Enum\Stage\StageSessionPermissionEnum;
use App\Exports\Admin\Stage\SessionExport;
use App\Helpers\AddOnsHelper;
use App\Livewire\Base\ExportComponent;
use App\Jobs\Admin\Stage\ProcessSessionExport;
use App\Models\StageSession;

class SessionExportComponent extends ExportComponent
{
    /**
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(StageSessionPermissionEnum::STAGE_SESSION_EXPORT->value);
        if (! AddOnsHelper::has(AddOnEnum::MODULES_STAGE_SESSION)) {
            abort(404);
        }
        if (! AddOnsHelper::has(AddOnEnum::FEATURES_EXPORT)) {
            abort(403);
        }
    }

    /**
     * @return string
     */
    protected function getExporterClass(): string
    {
        return SessionExport::class;
    }

    /**
     * @return string
     */
    protected function getProcessExportJobClass(): string
    {
        return ProcessSessionExport::class;
    }

    /**
     * @return string
     */
    protected function getViewName(): string
    {
        return "livewire.admin.stage.session.session-export-component";
    }

    /**
     * @return int
     */
    protected function getTotalRowsCount(): int
    {
        return StageSession::query()->count();
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
        return StageSessionPermissionEnum::STAGE_SESSION_EXPORT->value;
    }
}
