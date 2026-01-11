<?php

namespace Src\V1\Api\User\Enums;

/**
 * @enum PermissionEnum
 */
enum PermissionEnum: string
{
    /**
     * User Resource Permissions
     */
    case USER_VIEW = "user.view";
    case USER_CREATE = "user.create";
    case USER_UPDATE = "user.update";
    case USER_DELETE = "user.delete";
    case USER_RESTORE = "user.restore";
    case USER_FORCE_DELETE = "user.force-delete";

    /**
     * User Import Permissions
     */
    case USER_IMPORT_VIEW = "user.import.view";
    case USER_IMPORT_CREATE = "user.import.create";
    case USER_IMPORT_UPLOAD = "user.import.upload";
    case USER_IMPORT_DELETE = "user.import.delete";

    /**
     * User Export Permissions
     */
    case USER_EXPORT_VIEW = "user.export.view";
    case USER_EXPORT_CREATE = "user.export.create";
    case USER_EXPORT_DOWNLOAD = "user.export.download";
    case USER_EXPORT_DELETE = "user.export.delete";
}
