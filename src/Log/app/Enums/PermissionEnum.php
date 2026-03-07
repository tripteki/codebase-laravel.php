<?php

namespace Modules\Log\App\Enums;

/**
 * @enum PermissionEnum
 */
enum PermissionEnum: string
{
    /**
     * @var string
     */
    case ACTIVITY_VIEW = "activity.view";

    /**
     * @var string
     */
    case ACTIVITY_DELETE = "activity.delete";
}
