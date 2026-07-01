<?php

namespace Modules\Log\App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Acl\App\Models\Role;
use Modules\Log\App\Events\AdminRefresh;

class AdminRefreshBroadcaster
{
    /**
     * @var array<string, list<string>>
     */
    protected const SCOPE_PERMISSIONS = [
        "users" => [ "user.view", ],
        "roles" => [ "role.view", ],
        "permissions" => [ "permission.view", ],
        "activities" => [ "activity.view", ],
    ];

    /**
     * @var array<string, list<string>>
     */
    protected const SUBJECT_SCOPES = [
        "User" => [ "users", ],
        "Role" => [ "roles", ],
        "Permission" => [ "permissions", ],
        "Setting" => [],
    ];

    /**
     * @param list<string> $scopes
     * @param string|null $exceptUserId
     * @return void
     */
    public static function broadcast(array $scopes, ?string $exceptUserId = null): void
    {
        try {
            $permissionNames = [ "activity.view", ];

            foreach ($scopes as $scope) {
                foreach (self::SCOPE_PERMISSIONS[$scope] ?? [] as $permission) {
                    $permissionNames[] = $permission;
                }
            }

            foreach (self::findUserIds($permissionNames) as $userId) {
                if ($userId === $exceptUserId) {
                    continue;
                }

                event(new AdminRefresh($userId, array_values($scopes)));
            }
        } catch (\Throwable $exception) {
            Log::warning("admin refresh broadcast failed", [
                "scopes" => $scopes,
                "message" => $exception->getMessage(),
            ]);
        }
    }

    /**
     * @param string|null $subjectType
     * @param string|null $causerId
     * @return void
     */
    public static function scheduleForSubject(?string $subjectType, ?string $causerId): void
    {
        if ($subjectType === null || ! array_key_exists($subjectType, self::SUBJECT_SCOPES)) {
            return;
        }

        self::broadcast(self::SUBJECT_SCOPES[$subjectType], $causerId);
    }

    /**
     * @param list<string> $permissionNames
     * @return list<string>
     */
    protected static function findUserIds(array $permissionNames): array
    {
        $roleIds = Role::query()
            ->where("guard_name", GuardEnum::WEB->value)
            ->where(function ($query) use ($permissionNames): void {
                $query->where("name", RoleEnum::SUPERADMIN->value)
                    ->orWhereHas("permissions", function ($permissions) use ($permissionNames): void {
                        $permissions->whereIn("name", $permissionNames);
                    });
            })
            ->pluck("id");

        if ($roleIds->isEmpty()) {
            return [];
        }

        $table = (string) config("permission.table_names.model_has_roles");
        $teamColumn = (string) config("permission.column_names.team_foreign_key");

        $query = DB::table($table)
            ->where("model_type", User::class)
            ->whereIn("role_id", $roleIds);

        if (is_tenancy()) {
            $tenantId = current_tenant_id();

            if ($tenantId === null) {
                $query->where(function ($nested) use ($teamColumn): void {
                    $nested->whereNull($teamColumn)->orWhere($teamColumn, "");
                });
            } else {
                $query->where($teamColumn, $tenantId);
            }
        }

        return $query
            ->pluck("model_id")
            ->map(static fn (mixed $id): string => (string) $id)
            ->unique()
            ->values()
            ->all();
    }
}
