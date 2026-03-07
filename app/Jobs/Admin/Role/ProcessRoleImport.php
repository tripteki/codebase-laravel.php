<?php

namespace App\Jobs\Admin\Role;

use App\Jobs\Base\ProcessImportJob;
use App\Imports\Admin\Role\RoleImport;
use Src\V1\Api\Acl\Enums\GuardEnum;
use Src\V1\Api\Acl\Models\Permission;
use Src\V1\Api\Acl\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProcessRoleImport extends ProcessImportJob
{
    /**
     * @return string
     */
    protected function getImportClass(): string
    {
        return RoleImport::class;
    }

    /**
     * @param array $normalizedData
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidator(array $normalizedData): \Illuminate\Contracts\Validation\Validator
    {
        $defaultGuard = $normalizedData["guard_name"] ?? GuardEnum::WEB->value;
        $existingRole = Role::query()
            ->where("name", $normalizedData["name"] ?? "")
            ->where("guard_name", $defaultGuard)
            ->first();

        $permissionNames = [];
        if (isset($normalizedData["permissions"]) && ! empty($normalizedData["permissions"])) {
            $permissionNames = is_array($normalizedData["permissions"])
                ? $normalizedData["permissions"]
                : array_map("trim", explode(",", $normalizedData["permissions"]));
        }

        $tenant = config("tenancy.is_tenancy") ? tenant() : null;

        return Validator::make($normalizedData, [
            "name" => [
                "required",
                "string",
                "max:255",
                $existingRole
                    ? ($tenant ? $tenant->unique("roles", "name")->ignore($existingRole->id) : Rule::unique("roles", "name")->where("guard_name", $defaultGuard)->ignore($existingRole->id))
                    : ($tenant ? $tenant->unique("roles", "name") : Rule::unique("roles", "name")->where("guard_name", $defaultGuard)),
            ],
            "guard_name" => ["required", "string", "max:255"],
            "permissions" => ["nullable", "string"],
        ], [
            "name.required" => __("validation.required", ["attribute" => __("module_role.name")]),
            "name.max" => __("validation.max.string", ["attribute" => __("module_role.name"), "max" => 255]),
            "name.unique" => __("validation.unique", ["attribute" => __("module_role.name")]),
            "guard_name.required" => __("validation.required", ["attribute" => __("module_role.guard_name")]),
            "guard_name.max" => __("validation.max.string", ["attribute" => __("module_role.guard_name"), "max" => 255]),
        ]);
    }

    /**
     * @param array $validatedData
     * @param array $normalizedData
     * @return void
     * @throws \Exception
     */
    protected function validateBeforeProcess(array $validatedData, array $normalizedData): void
    {
        if (isset($normalizedData["permissions"]) && ! empty($normalizedData["permissions"])) {
            $permissionNames = is_array($normalizedData["permissions"])
                ? $normalizedData["permissions"]
                : array_map("trim", explode(",", $normalizedData["permissions"]));

            $guardName = $validatedData["guard_name"] ?? GuardEnum::WEB->value;
            $existingPermissions = Permission::query()
                ->where("guard_name", $guardName)
                ->whereIn("name", $permissionNames)
                ->pluck("name")
                ->toArray();

            $missingPermissions = array_diff($permissionNames, $existingPermissions);

            if (! empty($missingPermissions)) {
                throw new \Exception(__("module_role.permissions_not_found", ["permissions" => implode(", ", $missingPermissions)]));
            }
        }
    }

    /**
     * @param array $validatedData
     * @param array $normalizedData
     * @return void
     */
    protected function processRow(array $validatedData, array $normalizedData): void
    {
        $defaultGuard = $validatedData["guard_name"] ?? GuardEnum::WEB->value;
        $existingRole = Role::query()
            ->where("name", $validatedData["name"])
            ->where("guard_name", $defaultGuard)
            ->first();

        if ($existingRole) {
            $role = $existingRole;
            $role->name = $validatedData["name"];
            $role->guard_name = $defaultGuard;
        } else {
            $role = new Role();
            $role->name = $validatedData["name"];
            $role->guard_name = $defaultGuard;
        }

        $role->save();

        if (isset($normalizedData["permissions"])) {
            if (! empty($normalizedData["permissions"])) {
                $permissionNames = is_array($normalizedData["permissions"])
                    ? $normalizedData["permissions"]
                    : array_map("trim", explode(",", $normalizedData["permissions"]));

                $permissions = Permission::query()
                    ->where("guard_name", $defaultGuard)
                    ->whereIn("name", $permissionNames)
                    ->pluck("id")
                    ->toArray();

                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions([]);
            }
        }
    }
}
