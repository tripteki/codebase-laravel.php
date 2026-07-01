<?php

namespace Modules\Log\App\Enums;

/**
 * @enum \Modules\Log\App\Enums\PermissionEnum
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
