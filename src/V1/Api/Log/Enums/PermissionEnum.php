<?php

namespace Src\V1\Api\Log\Enums;

/**
 * @enum PermissionEnum
 */
enum PermissionEnum: string
{
    case ACTIVITY_VIEW = "activity.view";
    case ACTIVITY_DELETE = "activity.delete";
}
