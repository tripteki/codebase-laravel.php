<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StageMeeting;
use App\Models\StageSession;
use App\Models\Tenant;
use App\Models\User;
use App\Helpers\Tenant\TenantAccess;
use Src\V1\Api\Acl\Enums\RoleEnum;
use Src\V1\Api\Acl\Models\Permission;
use Src\V1\Api\Acl\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->input("q", "");
        $category = $request->input("category", "all");

        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                "results" => [],
            ]);
        }

        $results = [];
        $currentUser = $request->user();
        $user = $currentUser instanceof User ? $currentUser : null;
        $access = TenantAccess::forUser($user);

        $searchUsers = ($category === "all" || $category === "users") && $access->canAccountUsers;
        $searchEvents = ($category === "all" || $category === "events") && $access->canSearchEventsCategory();
        $searchRoles = ($category === "all" || $category === "roles") && $access->canAccessRoles;
        $searchPermissions = ($category === "all" || $category === "permissions") && $access->canAccessPermissions;
        $searchMeetings = ($category === "all" || $category === "meetings") && $access->canSearchMeetingsCategory();
        $searchSessions = ($category === "all" || $category === "sessions") && $access->canSearchSessionsCategory();

        if ($searchUsers) {
            $userQuery = User::query()
            ->with("profile")
            ->where(function ($q) use ($query) {
                $q->where("name", "like", "%{$query}%")
                    ->orWhere("email", "like", "%{$query}%");
            })
            ->limit(5);

            if (config("tenancy.is_tenancy") && $currentUser && ! $currentUser->hasRole(RoleEnum::SUPERADMIN->value)) {
                $userQuery->where("tenant_id", $currentUser->tenant_id);
            }

            $users = $userQuery->get(["id", "name", "email"])
            ->map(function (User $user) {
                return [
                    "id" => $user->id,
                    "title" => $user->name,
                    "subtitle" => $user->email,
                    "avatar" => $user->profile?->avatar ? asset("storage/" . $user->profile->avatar) : null,
                    "url" => tenant_routes("admin.users.show", $user),
                ];
            });

            if ($users->isNotEmpty()) {
                $results[] = [
                    "category" => __("sidebar.users"),
                    "items" => $users,
                ];
            }
        }

        if ($searchEvents) {
            $events = Tenant::query()
                ->where(function ($q) use ($query) {
                    $q->where("id", "like", "%{$query}%")
                        ->orWhere("data->title", "like", "%{$query}%");
                })
                ->limit(5)
                ->get(["id", "data"])
                ->map(function (Tenant $tenant) {
                    $title = data_get($tenant->data, "title");
                    $title = is_string($title) && $title !== "" ? $title : (string) $tenant->id;

                    return [
                        "id" => $tenant->id,
                        "title" => $title,
                        "subtitle" => (string) $tenant->id,
                        "url" => tenant_routes("admin.tenants.events.show", $tenant),
                    ];
                });

            if ($events->isNotEmpty()) {
                $results[] = [
                    "category" => __("sidebar.event"),
                    "items" => $events->toArray(),
                ];
            }
        }

        if ($searchRoles) {
            $roles = Role::query()
                ->where("name", "like", "%{$query}%")
                ->limit(5)
                ->get(["id", "name", "guard_name"])
                ->map(function (Role $role) {
                    return [
                        "id" => $role->id,
                        "title" => $role->name,
                        "subtitle" => (string) $role->guard_name,
                        "url" => tenant_routes("admin.roles.show", $role),
                    ];
                });

            if ($roles->isNotEmpty()) {
                $results[] = [
                    "category" => __("sidebar.roles"),
                    "items" => $roles->toArray(),
                ];
            }
        }

        if ($searchPermissions) {
            $permissions = Permission::query()
                ->where("name", "like", "%{$query}%")
                ->limit(5)
                ->get(["id", "name", "guard_name"])
                ->map(function (Permission $permission) {
                    return [
                        "id" => $permission->id,
                        "title" => $permission->name,
                        "subtitle" => (string) $permission->guard_name,
                        "url" => tenant_routes("admin.permissions.show", $permission),
                    ];
                });

            if ($permissions->isNotEmpty()) {
                $results[] = [
                    "category" => __("sidebar.permissions"),
                    "items" => $permissions->toArray(),
                ];
            }
        }

        if ($searchMeetings) {
            $meetings = StageMeeting::query()
                ->where(function ($q) use ($query) {
                    $q->where("title", "like", "%{$query}%")
                        ->orWhere("description", "like", "%{$query}%");
                })
                ->limit(5)
                ->get(["id", "title", "description"])
                ->map(function (StageMeeting $meeting) {
                    $descriptionText = $meeting->description ? trim(strip_tags((string) $meeting->description)) : "";

                    return [
                        "id" => $meeting->id,
                        "title" => $meeting->title,
                        "subtitle" => $descriptionText !== "" ? \Illuminate\Support\Str::limit($descriptionText, 40) : "",
                        "url" => tenant_routes("admin.stage.meetings.show", $meeting),
                    ];
                });

            if ($meetings->isNotEmpty()) {
                $results[] = [
                    "category" => __("sidebar.meeting"),
                    "items" => $meetings->toArray(),
                ];
            }
        }

        if ($searchSessions) {
            $sessions = StageSession::query()
                ->where(function ($q) use ($query) {
                    $q->where("title", "like", "%{$query}%")
                        ->orWhere("description", "like", "%{$query}%");
                })
                ->limit(5)
                ->get(["id", "title", "description"])
                ->map(function (StageSession $session) {
                    $descriptionText = $session->description ? trim(strip_tags((string) $session->description)) : "";

                    return [
                        "id" => $session->id,
                        "title" => $session->title,
                        "subtitle" => $descriptionText !== "" ? \Illuminate\Support\Str::limit($descriptionText, 40) : "",
                        "url" => tenant_routes("admin.stage.sessions.show", $session),
                    ];
                });

            if ($sessions->isNotEmpty()) {
                $results[] = [
                    "category" => __("sidebar.session"),
                    "items" => $sessions->toArray(),
                ];
            }
        }

        return response()->json([
            "results" => $results,
        ]);
    }
}
