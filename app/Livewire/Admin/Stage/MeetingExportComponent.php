<?php

namespace App\Livewire\Admin\Stage;

use App\Enum\Event\AddOnEnum;
use App\Enum\Stage\StageMeetingPermissionEnum;
use App\Exports\Admin\Stage\MeetingExport;
use App\Helpers\AddOnsHelper;
use App\Livewire\Base\ExportComponent;
use App\Jobs\Admin\Stage\ProcessMeetingExport;
use App\Models\StageMeeting;

class MeetingExportComponent extends ExportComponent
{
    /**
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(StageMeetingPermissionEnum::STAGE_MEETING_EXPORT->value);
        if (! AddOnsHelper::has(AddOnEnum::MODULES_STAGE_MEETING)) {
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
        return MeetingExport::class;
    }

    /**
     * @return string
     */
    protected function getProcessExportJobClass(): string
    {
        return ProcessMeetingExport::class;
    }

    /**
     * @return string
     */
    protected function getViewName(): string
    {
        return "livewire.admin.stage.meeting.meeting-export-component";
    }

    /**
     * @return int
     */
    protected function getTotalRowsCount(): int
    {
        return StageMeeting::query()->count();
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
        return StageMeetingPermissionEnum::STAGE_MEETING_EXPORT->value;
    }
}
