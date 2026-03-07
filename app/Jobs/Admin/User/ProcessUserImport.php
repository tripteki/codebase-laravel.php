<?php

namespace App\Jobs\Admin\User;

use App\Jobs\Base\ProcessImportJob;
use App\Imports\Admin\User\UserImport;
use App\Models\User;
use App\Models\Profile;
use Src\V1\Api\Acl\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Stancl\Tenancy\Database\Models\Domain;

class ProcessUserImport extends ProcessImportJob
{
    /**
     * @return string
     */
    protected function getImportClass(): string
    {
        return UserImport::class;
    }

    /**
     * @param array $rowData
     * @return array
     */
    protected function normalizeRowData(array $rowData): array
    {
        $normalizedData = parent::normalizeRowData($rowData);

        if (! config("tenancy.is_tenancy")) {
            unset($normalizedData["tenant_id"]);
            return $normalizedData;
        }

        $tenantIdValue = $normalizedData["tenant_id"] ?? null;

        if (filled($tenantIdValue) && str_contains((string) $tenantIdValue, ".")) {
            $domain = Domain::query()->where("domain", $tenantIdValue)->first();
            if ($domain) {
                $normalizedData["tenant_id"] = $domain->tenant_id;
            }
        }

        return $normalizedData;
    }

    /**
     * @param array $normalizedData
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidator(array $normalizedData): \Illuminate\Contracts\Validation\Validator
    {
        if (! config("tenancy.is_tenancy")) {
            $existingUser = User::query()->where("email", $normalizedData["email"] ?? "")->first();
            $emailRule = $existingUser
                ? Rule::unique("users", "email")->ignore($existingUser->id)
                : Rule::unique("users", "email");

            $rules = [
                "full_name" => ["nullable", "string", "max:255"],
                "email" => ["required", "string", "email", "max:255", $emailRule],
                "password" => ["nullable", "string", "min:8", "max:255"],
                "roles" => ["nullable", "string"],
            ];

            return Validator::make($normalizedData, $rules, [
                "email.required" => __("validation.required", ["attribute" => __("module_user.email")]),
                "email.email" => __("validation.email", ["attribute" => __("module_user.email")]),
                "email.max" => __("validation.max.string", ["attribute" => __("module_user.email"), "max" => 255]),
                "password.min" => __("validation.min.string", ["attribute" => __("module_user.password"), "min" => 8]),
                "password.max" => __("validation.max.string", ["attribute" => __("module_user.password"), "max" => 255]),
            ]);
        }

        $rowTenantId = $normalizedData["tenant_id"] ?? null;
        $existingQuery = User::query()->where("email", $normalizedData["email"] ?? "");

        if (filled($rowTenantId)) {
            $existingQuery->where("tenant_id", $rowTenantId);
        } else {
            $existingQuery->whereNull("tenant_id");
        }

        $existingUser = $existingQuery->first();

        $emailRule = filled($rowTenantId)
            ? ($existingUser
                ? Rule::unique("users", "email")->where("tenant_id", $rowTenantId)->ignore($existingUser->id)
                : Rule::unique("users", "email")->where("tenant_id", $rowTenantId))
            : ($existingUser
                ? Rule::unique("users", "email")->whereNull("tenant_id")->ignore($existingUser->id)
                : Rule::unique("users", "email")->whereNull("tenant_id"));

        $rules = [
            "full_name" => ["nullable", "string", "max:255"],
            "email" => [
                "required",
                "string",
                "email",
                "max:255",
                $emailRule,
            ],
            "password" => ["nullable", "string", "min:8", "max:255"],
            "roles" => ["nullable", "string"],
            "tenant_id" => ["required", "string", "exists:tenants,id"],
        ];

        return Validator::make($normalizedData, $rules, [
            "email.required" => __("validation.required", ["attribute" => __("module_user.email")]),
            "email.email" => __("validation.email", ["attribute" => __("module_user.email")]),
            "email.max" => __("validation.max.string", ["attribute" => __("module_user.email"), "max" => 255]),
            "password.min" => __("validation.min.string", ["attribute" => __("module_user.password"), "min" => 8]),
            "password.max" => __("validation.max.string", ["attribute" => __("module_user.password"), "max" => 255]),
            "tenant_id.required" => __("validation.required", ["attribute" => __("module_user.tenant")]),
            "tenant_id.exists" => __("validation.exists", ["attribute" => __("module_user.tenant")]),
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
        $q = User::query()->where("email", $validatedData["email"]);

        if (config("tenancy.is_tenancy") && filled($validatedData["tenant_id"] ?? null)) {
            $q->where("tenant_id", $validatedData["tenant_id"]);
        } elseif (config("tenancy.is_tenancy")) {
            $q->whereNull("tenant_id");
        }

        $existingUser = $q->first();

        if (! $existingUser && empty($validatedData["password"])) {
            throw new \Exception(__("module_user.password_required_for_new_user"));
        }
    }

    /**
     * @param array $validatedData
     * @param array $normalizedData
     * @return void
     */
    protected function processRow(array $validatedData, array $normalizedData): void
    {
        $q = User::query()->where("email", $validatedData["email"]);

        if (config("tenancy.is_tenancy") && filled($validatedData["tenant_id"] ?? null)) {
            $q->where("tenant_id", $validatedData["tenant_id"]);
        } elseif (config("tenancy.is_tenancy")) {
            $q->whereNull("tenant_id");
        }

        $existingUser = $q->first();

        $nameFromEmail = Str::before($validatedData["email"], "@");

        if ($existingUser) {
            $user = $existingUser;
            $user->name = $nameFromEmail;

            if (isset($validatedData["password"]) && ! empty($validatedData["password"])) {
                $user->password = $validatedData["password"];
            }
        } else {
            $user = new User();
            $user->name = $nameFromEmail;
            $user->email = $validatedData["email"];
            $user->password = $validatedData["password"];
        }

        if (config("tenancy.is_tenancy") && isset($validatedData["tenant_id"]) && filled($validatedData["tenant_id"])) {
            $user->tenant_id = $validatedData["tenant_id"];
        }

        $user->save();

        $fullName = isset($validatedData["full_name"]) ? trim((string) $validatedData["full_name"]) : "";
        $profile = $user->profile;
        if ($fullName !== "") {
            if (! $profile) {
                $profile = new Profile();
                $profile->user_id = $user->id;
            }
            $profile->full_name = $fullName;
            $profile->save();
        } elseif ($profile) {
            $profile->full_name = null;
            $profile->save();
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        $roleNames = [];

        if (isset($normalizedData["roles"]) && ! empty($normalizedData["roles"])) {
            $roleNames = is_array($normalizedData["roles"])
                ? $normalizedData["roles"]
                : array_map("trim", explode(",", $normalizedData["roles"]));
        }

        if (! empty($roleNames)) {
            $roleIds = Role::query()
                ->whereIn("name", $roleNames)
                ->pluck("id")
                ->toArray();

            if (count($roleIds) !== count($roleNames)) {
                $foundRoles = Role::query()
                    ->whereIn("name", $roleNames)
                    ->pluck("name")
                    ->toArray();
                $missingRoles = array_diff($roleNames, $foundRoles);
                throw new \Exception(__("module_user.roles_not_found", ["roles" => implode(", ", $missingRoles)]));
            }
            $user->syncRoles($roleIds);
        } else {
            $user->syncRoles([]);
        }
    }
}
