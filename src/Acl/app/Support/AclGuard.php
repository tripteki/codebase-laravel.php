<?php

namespace Modules\Acl\App\Support;

use App\Http\Responses\ApiErrorResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
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
     * @param \Modules\Acl\App\Models\Permission $permission
     * @return bool
     */
    public static function isProtectedPermission(Permission $permission): bool
    {
        return self::isProtectedPermissionName($permission->name);
    }

    /**
     * @param \Modules\Acl\App\Models\Permission $permission
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
     * @param \Modules\Acl\App\Models\Role $role
     * @return bool
     */
    public static function isProtectedRole(Role $role): bool
    {
        return self::isProtectedRoleName($role->name);
    }

    /**
     * @param \Modules\Acl\App\Models\Role $role
     * @return void
     */
    public static function ensureRoleIsMutable(Role $role): void
    {
        if (self::isProtectedRole($role)) {
            throw new HttpException(422, __("acl.role.protected"));
        }
    }

    /**
     * @return string|null
     */
    public static function activeTeamId(): ?string
    {
        if (! function_exists("getPermissionsTeamId")) {
            return null;
        }

        $team = getPermissionsTeamId();

        if ($team === null || $team === "") {
            return null;
        }

        return (string) $team;
    }

    /**
     * @param string $name
     * @param string $guard
     * @param string|null $exceptId
     * @param string|null $tenantId
     * @return void
     */
    public static function ensureRoleNameAvailable(string $name, string $guard, ?string $exceptId = null, ?string $tenantId = null): void
    {
        if (self::nameIsTaken(Role::class, $name, $guard, $exceptId, $tenantId)) {
            throw new HttpResponseException(ApiErrorResponse::message(__("acl.role.name_exists"), 400));
        }
    }

    /**
     * @param string $name
     * @param string $guard
     * @param string|null $exceptId
     * @param string|null $tenantId
     * @return void
     */
    public static function ensurePermissionNameAvailable(string $name, string $guard, ?string $exceptId = null, ?string $tenantId = null): void
    {
        if (self::nameIsTaken(Permission::class, $name, $guard, $exceptId, $tenantId)) {
            throw new HttpResponseException(ApiErrorResponse::message(__("acl.permission.name_exists"), 400));
        }
    }

    /**
     * @param class-string<\Modules\Acl\App\Models\Role|\Modules\Acl\App\Models\Permission> $model
     * @param string $name
     * @param string $guard
     * @param string|null $exceptId
     * @param string|null $tenantId
     * @return bool
     */
    protected static function nameIsTaken(string $model, string $name, string $guard, ?string $exceptId, ?string $tenantId): bool
    {
        $name = trim($name);

        if ($name === "") {
            return false;
        }

        $query = $model::query()
            ->withoutGlobalScopes()
            ->where("name", $name)
            ->where("guard_name", $guard);

        if ($exceptId !== null && $exceptId !== "") {
            $query->whereKeyNot($exceptId);
        }

        if ($tenantId === null || $tenantId === "") {
            $query->where(function ($nested): void {
                $nested->whereNull("tenant_id")->orWhere("tenant_id", "");
            });
        } else {
            $query->where("tenant_id", $tenantId);
        }

        return $query->exists();
    }
}
