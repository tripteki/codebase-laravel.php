<?php

namespace Src\V1\Api\Acl\Enums;

/**
 * @enum PermissionEnum
 */
enum PermissionEnum: string
{
    /**
     * Role Resource Permissions
     */
    case ROLE_VIEW = "role.view";
    case ROLE_CREATE = "role.create";
    case ROLE_UPDATE = "role.update";
    case ROLE_DELETE = "role.delete";
    case ROLE_RESTORE = "role.restore";
    case ROLE_FORCE_DELETE = "role.force-delete";
    case ROLE_IMPORT = "role.import";
    case ROLE_EXPORT = "role.export";

    /**
     * Permission Resource Permissions
     */
    case PERMISSION_VIEW = "permission.view";
    case PERMISSION_CREATE = "permission.create";
    case PERMISSION_UPDATE = "permission.update";
    case PERMISSION_DELETE = "permission.delete";
    case PERMISSION_RESTORE = "permission.restore";
    case PERMISSION_FORCE_DELETE = "permission.force-delete";
    case PERMISSION_IMPORT = "permission.import";
    case PERMISSION_EXPORT = "permission.export";
}
