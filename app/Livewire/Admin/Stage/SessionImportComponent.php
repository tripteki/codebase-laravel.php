<?php

namespace App\Livewire\Admin\Stage;

use App\Enum\Event\AddOnEnum;
use App\Enum\Stage\StageSessionPermissionEnum;
use App\Helpers\AddOnsHelper;
use App\Imports\Admin\Stage\SessionImport;
use App\Jobs\Admin\Stage\ProcessSessionImport;
use App\Livewire\Base\ImportComponent;

class SessionImportComponent extends ImportComponent
{
    /**
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(StageSessionPermissionEnum::STAGE_SESSION_IMPORT->value);
        if (! AddOnsHelper::has(AddOnEnum::MODULES_STAGE_SESSION)) {
            abort(404);
        }
        if (! AddOnsHelper::has(AddOnEnum::FEATURES_IMPORT)) {
            abort(403);
        }
    }

    /**
     * @return string
     */
    protected function getImporterClass(): string
    {
        return SessionImport::class;
    }

    /**
     * @return string
     */
    protected function getProcessImportJobClass(): string
    {
        return ProcessSessionImport::class;
    }

    /**
     * @return string
     */
    protected function getViewName(): string
    {
        return "livewire.admin.stage.session.session-import-component";
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
        return StageSessionPermissionEnum::STAGE_SESSION_IMPORT->value;
    }
}
