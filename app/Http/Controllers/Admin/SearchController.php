<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Src\V1\Api\Acl\Models\Role;
use Src\V1\Api\Acl\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Search across multiple resources.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->input("q", "");

        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                "results" => [],
            ]);
        }

        $results = [];

        $users = User::query()
            ->where(function ($q) use ($query) {
                $q->where("name", "like", "%{$query}%")
                    ->orWhere("email", "like", "%{$query}%");
            })
            ->limit(5)
            ->get(["id", "name", "email"])
            ->map(function ($user) {
                return [
                    "id" => $user->id,
                    "title" => $user->name,
                    "subtitle" => $user->email,
                    "url" => route("admin.users.show", $user),
                ];
            });

        if ($users->isNotEmpty()) {
            $results[] = [
                "category" => __("module_user.title"),
                "items" => $users,
            ];
        }

        $roles = Role::query()
            ->where(function ($q) use ($query) {
                $q->where("name", "like", "%{$query}%")
                    ->orWhere("guard_name", "like", "%{$query}%");
            })
            ->limit(5)
            ->get(["id", "name", "guard_name"])
            ->map(function ($role) {
                return [
                    "id" => $role->id,
                    "title" => $role->name,
                    "subtitle" => $role->guard_name,
                    "url" => route("admin.roles.show", $role),
                ];
            });

        if ($roles->isNotEmpty()) {
            $results[] = [
                "category" => __("module_role.title"),
                "items" => $roles,
            ];
        }

        $permissions = Permission::query()
            ->where(function ($q) use ($query) {
                $q->where("name", "like", "%{$query}%")
                    ->orWhere("guard_name", "like", "%{$query}%");
            })
            ->limit(5)
            ->get(["id", "name", "guard_name"])
            ->map(function ($permission) {
                return [
                    "id" => $permission->id,
                    "title" => $permission->name,
                    "subtitle" => $permission->guard_name,
                    "url" => route("admin.permissions.show", $permission),
                ];
            });

        if ($permissions->isNotEmpty()) {
            $results[] = [
                "category" => __("module_permission.title"),
                "items" => $permissions,
            ];
        }

        return response()->json([
            "results" => $results,
        ]);
    }
}
