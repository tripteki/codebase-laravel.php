<?php

namespace Modules\Notification\App\Enums;

/**
 * @enum PermissionEnum
 */
enum PermissionEnum: string
{
    /**
     * @var string
     */
    case NOTIFICATION_VIEW = "notification.view";

    /**
     * @var string
     */
    case NOTIFICATION_DELETE = "notification.delete";

    /**
     * @var string
     */
    case NOTIFICATION_RESTORE = "notification.restore";
}
