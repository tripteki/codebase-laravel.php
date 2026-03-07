<?php

namespace App\Livewire\Admin\Stage;

use App\Enum\Event\AddOnEnum;
use App\Enum\Stage\StageMeetingPermissionEnum;
use App\Helpers\AddOnsHelper;
use App\Imports\Admin\Stage\MeetingImport;
use App\Jobs\Admin\Stage\ProcessMeetingImport;
use App\Livewire\Base\ImportComponent;

class MeetingImportComponent extends ImportComponent
{
    /**
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(StageMeetingPermissionEnum::STAGE_MEETING_IMPORT->value);
        if (! AddOnsHelper::has(AddOnEnum::MODULES_STAGE_MEETING)) {
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
        return MeetingImport::class;
    }

    /**
     * @return string
     */
    protected function getProcessImportJobClass(): string
    {
        return ProcessMeetingImport::class;
    }

    /**
     * @return string
     */
    protected function getViewName(): string
    {
        return "livewire.admin.stage.meeting.meeting-import-component";
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
        return StageMeetingPermissionEnum::STAGE_MEETING_IMPORT->value;
    }
}
