<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\AdminTenancySupport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\PermissionEnum as AclPermissionEnum;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Acl\App\Models\Permission;
use Modules\Acl\App\Models\Role;
use Modules\Event\App\Enums\PermissionEnum as EventPermissionEnum;
use Modules\Event\App\Models\Event;
use Modules\User\App\Enums\PermissionEnum as UserPermissionEnum;
use OpenApi\Attributes as OA;

class AdminSearchController extends Controller
{
    #[OA\Get(
        path: "/api/v1/admin/search",
        tags: ["Admin Search"],
        summary: "Global admin search",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "q", in: "query", required: true, description: "Search query (min 2 chars)."),
            new OA\Parameter(name: "category", in: "query", required: false, description: "Category: all, users, roles, permissions, events (events: central only)."),
        ],
        responses: [
            new OA\Response(response: 200, description: "Success."),
            new OA\Response(response: 401, description: "Unauthorized."),
        ],
    )]
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = trim((string) $request->input("q", ""));
        $category = (string) $request->input("category", "all");
        $user = $request->user();

        if ($query === "" || strlen($query) < 2) {
            return response()->json([
                "results" => [],
            ], 200);
        }

        $results = [];

        $searchEvents = is_central()
            && ($category === "all" || $category === "events")
            && (
                is_central_superadmin($user)
                || $user->hasPermissionTo(
                    EventPermissionEnum::EVENT_VIEW->value,
                    GuardEnum::WEB->value,
                )
            );

        if ($searchEvents) {
            $events = Event::query()
                ->where(function ($nested) use ($query): void {
                    $nested->where("id", "like", "%{$query}%");

                    $driver = $nested->getConnection()->getDriverName();

                    if ($driver === "mysql") {
                        $nested->orWhereRaw(
                            "JSON_UNQUOTE(JSON_EXTRACT(`data`, '$.title')) LIKE ?",
                            ["%{$query}%"],
                        );

                        return;
                    }

                    if ($driver === "sqlite") {
                        $nested->orWhereRaw(
                            "json_extract(`data`, '$.title') LIKE ?",
                            ["%{$query}%"],
                        );
                    }
                })
                ->limit(5)
                ->get()
                ->map(function (Event $model): array {
                    $id = (string) $model->getKey();
                    $title = trim((string) ($model->getAttribute("title") ?? $id));

                    return [
                        "id" => $id,
                        "title" => $title !== "" ? $title : $id,
                        "subtitle" => $title !== "" && $title !== $id ? $id : null,
                        "url" => $this->adminSearchUrl("events/".$id),
                    ];
                })
                ->values()
                ->all();

            if ($events !== []) {
                $results[] = [
                    "category" => "events",
                    "items" => $events,
                ];
            }
        }

        $searchUsers = ($category === "all" || $category === "users")
            && $user->hasPermissionTo(UserPermissionEnum::USER_VIEW->value, GuardEnum::WEB->value);

        if ($searchUsers) {
            $users = $this->scopedUserQuery()
                ->where(function ($builder) use ($query): void {
                    $builder->where("name", "like", "%{$query}%")
                        ->orWhere("email", "like", "%{$query}%");
                })
                ->limit(5)
                ->get(["id", "name", "email"])
                ->map(function (User $model): array {
                    $name = trim((string) $model->name);
                    $email = trim((string) $model->email);

                    return [
                        "id" => (string) $model->getKey(),
                        "title" => $name !== "" ? $name : $email,
                        "subtitle" => $name !== "" && $email !== "" ? $email : null,
                        "url" => $this->adminSearchUrl("users/".$model->getKey()),
                    ];
                })
                ->values()
                ->all();

            if ($users !== []) {
                $results[] = [
                    "category" => "users",
                    "items" => $users,
                ];
            }
        }

        $searchRoles = ($category === "all" || $category === "roles")
            && $user->hasPermissionTo(AclPermissionEnum::ROLE_VIEW->value, GuardEnum::WEB->value);

        if ($searchRoles) {
            $roles = $this->scopedRoleQuery()
                ->where("name", "like", "%{$query}%")
                ->limit(5)
                ->get(["id", "name", "guard_name"])
                ->map(fn (Role $model): array => [
                    "id" => (string) $model->getKey(),
                    "title" => (string) $model->name,
                    "subtitle" => (string) $model->guard_name,
                    "url" => $this->adminSearchUrl("roles/".$model->getKey()),
                ])
                ->values()
                ->all();

            if ($roles !== []) {
                $results[] = [
                    "category" => "roles",
                    "items" => $roles,
                ];
            }
        }

        $searchPermissions = ($category === "all" || $category === "permissions")
            && $user->hasPermissionTo(AclPermissionEnum::PERMISSION_VIEW->value, GuardEnum::WEB->value);

        if ($searchPermissions) {
            $permissions = $this->scopedPermissionQuery()
                ->where("name", "like", "%{$query}%")
                ->limit(5)
                ->get(["id", "name", "guard_name"])
                ->map(fn (Permission $model): array => [
                    "id" => (string) $model->getKey(),
                    "title" => (string) $model->name,
                    "subtitle" => (string) $model->guard_name,
                    "url" => $this->adminSearchUrl("permissions/".$model->getKey()),
                ])
                ->values()
                ->all();

            if ($permissions !== []) {
                $results[] = [
                    "category" => "permissions",
                    "items" => $permissions,
                ];
            }
        }

        return response()->json([
            "results" => $results,
        ], 200);
    }

    /**
     * @return Builder<User>
     */
    private function scopedUserQuery(): Builder
    {
        $query = User::query()->withTrashed();
        AdminTenancySupport::applyActiveTenantScope($query);

        return $query;
    }

    /**
     * @return Builder<Role>
     */
    private function scopedRoleQuery(): Builder
    {
        $query = Role::query();
        AdminTenancySupport::applyActiveTenantScope($query);

        if (current_tenant_id() !== null) {
            $query->where("name", "!=", RoleEnum::SUPERADMIN->value);
        }

        return $query;
    }

    /**
     * @return Builder<Permission>
     */
    private function scopedPermissionQuery(): Builder
    {
        $query = Permission::query();
        AdminTenancySupport::applyActiveTenantScope($query);

        return $query;
    }

    /**
     * @param string $path
     * @return string
     */
    private function adminSearchUrl(string $path): string
    {
        return admin_frontend_prefix()."/".ltrim($path, "/");
    }
}
