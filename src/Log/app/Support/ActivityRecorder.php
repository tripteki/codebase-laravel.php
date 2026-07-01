<?php

namespace Modules\Log\App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\Acl\App\Models\Permission;
use Modules\Acl\App\Models\Role;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Contracts\Role as RoleContract;

class ActivityRecorder
{
    /**
     * @param \Modules\Acl\App\Models\Role $role
     * @param list<string> $previousPermissionNames
     * @param list<string> $currentPermissionNames
     * @return void
     */
    public static function rolePermissionsSynced(
        Role $role,
        array $previousPermissionNames,
        array $currentPermissionNames,
    ): void {
        sort($previousPermissionNames);
        sort($currentPermissionNames);

        if ($previousPermissionNames === $currentPermissionNames) {
            return;
        }

        self::log(
            description: "role permissions synced",
            subject: $role,
            event: "permissions_synced",
            properties: [
                "old" => [ "permissions" => $previousPermissionNames, ],
                "attributes" => [ "permissions" => $currentPermissionNames, ],
            ],
        );
    }

    /**
     * @param \App\Models\User $user
     * @param list<string> $roleNames
     * @return void
     */
    public static function userRolesAttached(User $user, array $roleNames): void
    {
        if ($roleNames === []) {
            return;
        }

        self::log(
            description: "user role attached",
            subject: $user,
            event: "role_attached",
            properties: [
                "attributes" => [ "roles" => array_values($roleNames), ],
            ],
        );
    }

    /**
     * @param \App\Models\User $user
     * @param list<string> $roleNames
     * @return void
     */
    public static function userRolesDetached(User $user, array $roleNames): void
    {
        if ($roleNames === []) {
            return;
        }

        self::log(
            description: "user role detached",
            subject: $user,
            event: "role_detached",
            properties: [
                "old" => [ "roles" => array_values($roleNames), ],
            ],
        );
    }

    /**
     * @param \App\Models\User $user
     * @param list<string> $permissionNames
     * @return void
     */
    public static function userPermissionsAttached(User $user, array $permissionNames): void
    {
        if ($permissionNames === []) {
            return;
        }

        self::log(
            description: "user permission attached",
            subject: $user,
            event: "permission_attached",
            properties: [
                "attributes" => [ "permissions" => array_values($permissionNames), ],
            ],
        );
    }

    /**
     * @param \App\Models\User $user
     * @param list<string> $permissionNames
     * @return void
     */
    public static function userPermissionsDetached(User $user, array $permissionNames): void
    {
        if ($permissionNames === []) {
            return;
        }

        self::log(
            description: "user permission detached",
            subject: $user,
            event: "permission_detached",
            properties: [
                "old" => [ "permissions" => array_values($permissionNames), ],
            ],
        );
    }

    /**
     * @param \App\Models\User $user
     * @return void
     */
    public static function userEmailVerified(User $user): void
    {
        self::log(
            description: "user email verified",
            subject: $user,
            event: "verified",
            properties: [
                "attributes" => [
                    "email_verified_at" => $user->email_verified_at?->toIso8601String(),
                ],
            ],
        );
    }

    /**
     * @param mixed $rolesOrIds
     * @return list<string>
     */
    public static function resolveRoleNames(mixed $rolesOrIds): array
    {
        return self::resolveNames($rolesOrIds, Role::class);
    }

    /**
     * @param mixed $permissionsOrIds
     * @return list<string>
     */
    public static function resolvePermissionNames(mixed $permissionsOrIds): array
    {
        return self::resolveNames($permissionsOrIds, Permission::class);
    }

    /**
     * @param mixed $items
     * @param class-string<Model> $modelClass
     * @return list<string>
     */
    protected static function resolveNames(mixed $items, string $modelClass): array
    {
        if ($items instanceof Collection) {
            $items = $items->all();
        }

        if ($items instanceof Model) {
            $items = [ $items, ];
        }

        if (! is_array($items)) {
            $items = [ $items, ];
        }

        $names = [];

        foreach ($items as $item) {
            if ($item instanceof RoleContract || $item instanceof PermissionContract) {
                $names[] = (string) $item->name;

                continue;
            }

            if ($item instanceof Model && isset($item->name)) {
                $names[] = (string) $item->name;

                continue;
            }

            if (is_string($item) && $item !== "") {
                $record = $modelClass::query()->find($item);

                if ($record !== null) {
                    $names[] = (string) $record->name;

                    continue;
                }

                $names[] = $item;
            }
        }

        return array_values(array_unique($names));
    }

    /**
     * @param string $description
     * @param \Illuminate\Database\Eloquent\Model|null $subject
     * @param string $event
     * @param array<string, mixed> $properties
     * @return void
     */
    protected static function log(
        string $description,
        ?Model $subject,
        string $event,
        array $properties,
    ): void {
        if (! self::shouldRecord()) {
            return;
        }

        $logger = activity()
            ->event($event)
            ->withProperties($properties);

        if ($subject !== null) {
            $logger->performedOn($subject);
        }

        $causer = Auth::user();

        if ($causer instanceof User) {
            $logger->causedBy($causer);
        }

        $logger->log($description);
    }

    /**
     * @return bool
     */
    protected static function shouldRecord(): bool
    {
        if (app()->runningUnitTests()) {
            return true;
        }

        if (app()->runningInConsole()) {
            return Auth::check();
        }

        return true;
    }
}
