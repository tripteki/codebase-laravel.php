<?php

namespace Modules\Acl\App\Support;

use Modules\Acl\App\Enums\PermissionEnum as AclPermissionEnum;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Acl\App\Models\Permission;
use Modules\Acl\App\Models\Role;
use Modules\Log\App\Enums\PermissionEnum as LogPermissionEnum;
use Modules\Notification\App\Enums\PermissionEnum as NotificationPermissionEnum;
use Modules\User\App\Enums\PermissionEnum as UserPermissionEnum;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AclGuard
{
    /**
     * @return list<string>
     */
    public static function protectedPermissionNames(): array
    {
        static $names = null;

        if ($names !== null) {
            return $names;
        }

        $names = [];

        foreach ([
            AclPermissionEnum::class,
            UserPermissionEnum::class,
            LogPermissionEnum::class,
            NotificationPermissionEnum::class,
        ] as $enumClass) {
            foreach ($enumClass::cases() as $permission) {
                $names[] = $permission->value;
            }
        }

        $names = array_values(array_unique($names));

        return $names;
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function isProtectedPermissionName(string $name): bool
    {
        return in_array($name, self::protectedPermissionNames(), true);
    }

    /**
     * @param Permission $permission
     * @return bool
     */
    public static function isProtectedPermission(Permission $permission): bool
    {
        return self::isProtectedPermissionName($permission->name);
    }

    /**
     * @param Permission $permission
     * @return void
     */
    public static function ensurePermissionIsMutable(Permission $permission): void
    {
        if (self::isProtectedPermission($permission)) {
            throw new HttpException(422, __("acl.permission.protected"));
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function isProtectedRoleName(string $name): bool
    {
        $protected = array_map(
            fn (RoleEnum $roleEnum) => $roleEnum->value,
            RoleEnum::cases(),
        );

        return in_array($name, $protected, true);
    }

    /**
     * @param Role $role
     * @return bool
     */
    public static function isProtectedRole(Role $role): bool
    {
        return self::isProtectedRoleName($role->name);
    }

    /**
     * @param Role $role
     * @return void
     */
    public static function ensureRoleIsMutable(Role $role): void
    {
        if (self::isProtectedRole($role)) {
            throw new HttpException(422, __("acl.role.protected"));
        }
    }
}
