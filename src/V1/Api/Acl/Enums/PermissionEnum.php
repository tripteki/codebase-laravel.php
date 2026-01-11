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

    /**
     * Role Import Permissions
     */
    case ROLE_IMPORT_VIEW = "role.import.view";
    case ROLE_IMPORT_CREATE = "role.import.create";
    case ROLE_IMPORT_UPLOAD = "role.import.upload";
    case ROLE_IMPORT_DELETE = "role.import.delete";

    /**
     * Role Export Permissions
     */
    case ROLE_EXPORT_VIEW = "role.export.view";
    case ROLE_EXPORT_CREATE = "role.export.create";
    case ROLE_EXPORT_DOWNLOAD = "role.export.download";
    case ROLE_EXPORT_DELETE = "role.export.delete";

    /**
     * Permission Resource Permissions
     */
    case PERMISSION_VIEW = "permission.view";
    case PERMISSION_CREATE = "permission.create";
    case PERMISSION_UPDATE = "permission.update";
    case PERMISSION_DELETE = "permission.delete";

    /**
     * Permission Import Permissions
     */
    case PERMISSION_IMPORT_VIEW = "permission.import.view";
    case PERMISSION_IMPORT_CREATE = "permission.import.create";
    case PERMISSION_IMPORT_UPLOAD = "permission.import.upload";
    case PERMISSION_IMPORT_DELETE = "permission.import.delete";

    /**
     * Permission Export Permissions
     */
    case PERMISSION_EXPORT_VIEW = "permission.export.view";
    case PERMISSION_EXPORT_CREATE = "permission.export.create";
    case PERMISSION_EXPORT_DOWNLOAD = "permission.export.download";
    case PERMISSION_EXPORT_DELETE = "permission.export.delete";
}
