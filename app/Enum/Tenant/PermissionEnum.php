<?php

namespace App\Enum\Tenant;

/**
 * @enum PermissionEnum
 */
enum PermissionEnum: string
{
    /**
     * @var string
     */
    case EVENT_VIEW = "event.view";

    /**
     * @var string
     */
    case EVENT_CREATE = "event.create";

    /**
     * @var string
     */
    case EVENT_UPDATE = "event.update";

    /**
     * @var string
     */
    case EVENT_DELETE = "event.delete";

    /**
     * @var string
     */
    case EVENT_IMPORT = "event.import";

    /**
     * @var string
     */
    case EVENT_EXPORT = "event.export";
}
