<?php

namespace App\Enum\Stage;

/**
 * @enum StageSessionPermissionEnum
 */
enum StageSessionPermissionEnum: string
{
    /**
     * @var string
     */
    case STAGE_SESSION_CREATE = "stage.session.create";

    /**
     * @var string
     */
    case STAGE_SESSION_VIEW = "stage.session.view";

    /**
     * @var string
     */
    case STAGE_SESSION_UPDATE = "stage.session.update";

    /**
     * @var string
     */
    case STAGE_SESSION_DELETE = "stage.session.delete";

    /**
     * @var string
     */
    case STAGE_SESSION_IMPORT = "stage.session.import";

    /**
     * @var string
     */
    case STAGE_SESSION_EXPORT = "stage.session.export";
}
