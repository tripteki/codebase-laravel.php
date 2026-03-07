<?php

namespace App\Enum\Stage;

/**
 * @enum StageMeetingPermissionEnum
 */
enum StageMeetingPermissionEnum: string
{
    /**
     * @var string
     */
    case STAGE_MEETING_CREATE = "stage.meeting.create";

    /**
     * @var string
     */
    case STAGE_MEETING_VIEW = "stage.meeting.view";

    /**
     * @var string
     */
    case STAGE_MEETING_UPDATE = "stage.meeting.update";

    /**
     * @var string
     */
    case STAGE_MEETING_DELETE = "stage.meeting.delete";

    /**
     * @var string
     */
    case STAGE_MEETING_IMPORT = "stage.meeting.import";

    /**
     * @var string
     */
    case STAGE_MEETING_EXPORT = "stage.meeting.export";
}
