<?php

namespace App\Jobs\Admin\Permission;

use App\Jobs\Base\ProcessImportJob;
use App\Imports\Admin\Permission\PermissionImport;
use Src\V1\Api\Acl\Enums\GuardEnum;
use Src\V1\Api\Acl\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProcessPermissionImport extends ProcessImportJob
{
    /**
     * @return string
     */
    protected function getImportClass(): string
    {
        return PermissionImport::class;
    }

    /**
     * @param array $normalizedData
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidator(array $normalizedData): \Illuminate\Contracts\Validation\Validator
    {
        $defaultGuard = $normalizedData["guard_name"] ?? GuardEnum::WEB->value;
        $existingPermission = Permission::query()
            ->where("name", $normalizedData["name"] ?? "")
            ->where("guard_name", $defaultGuard)
            ->first();

        $tenant = config("tenancy.is_tenancy") ? tenant() : null;

        return Validator::make($normalizedData, [
            "name" => [
                "required",
                "string",
                "max:255",
                $existingPermission
                    ? ($tenant ? $tenant->unique("permissions", "name")->ignore($existingPermission->id) : Rule::unique("permissions", "name")->where("guard_name", $defaultGuard)->ignore($existingPermission->id))
                    : ($tenant ? $tenant->unique("permissions", "name") : Rule::unique("permissions", "name")->where("guard_name", $defaultGuard)),
            ],
            "guard_name" => ["required", "string", "max:255"],
        ], [
            "name.required" => __("validation.required", ["attribute" => __("module_permission.name")]),
            "name.max" => __("validation.max.string", ["attribute" => __("module_permission.name"), "max" => 255]),
            "name.unique" => __("validation.unique", ["attribute" => __("module_permission.name")]),
            "guard_name.required" => __("validation.required", ["attribute" => __("module_permission.guard_name")]),
            "guard_name.max" => __("validation.max.string", ["attribute" => __("module_permission.guard_name"), "max" => 255]),
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
        //
    }

    /**
     * @param array $validatedData
     * @param array $normalizedData
     * @return void
     */
    protected function processRow(array $validatedData, array $normalizedData): void
    {
        $defaultGuard = $validatedData["guard_name"] ?? GuardEnum::WEB->value;
        $existingPermission = Permission::query()
            ->where("name", $validatedData["name"])
            ->where("guard_name", $defaultGuard)
            ->first();

        if ($existingPermission) {
            $permission = $existingPermission;
            $permission->name = $validatedData["name"];
            $permission->guard_name = $defaultGuard;
        } else {
            $permission = new Permission();
            $permission->name = $validatedData["name"];
            $permission->guard_name = $defaultGuard;
        }

        $permission->save();
    }
}
