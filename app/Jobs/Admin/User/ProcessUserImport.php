<?php

namespace App\Jobs\Admin\User;

use App\Jobs\Base\ProcessImportJob;
use App\Imports\Admin\User\UserImport;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProcessUserImport extends ProcessImportJob
{
    /**
     * Get the import class name.
     *
     * @return string
     */
    protected function getImportClass(): string
    {
        return UserImport::class;
    }

    /**
     * Get validator for row data.
     *
     * @param array $normalizedData
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidator(array $normalizedData): \Illuminate\Contracts\Validation\Validator
    {
        $existingUser = User::query()->where("email", $normalizedData["email"] ?? "")->first();

        return Validator::make($normalizedData, [
            "name" => ["required", "string", "min:2", "max:255"],
            "email" => [
                "required",
                "string",
                "email",
                "max:255",
                $existingUser ? Rule::unique(User::class)->ignore($existingUser->id) : Rule::unique(User::class),
            ],
            "password" => ["nullable", "string", "min:8", "max:255"],
        ], [
            "name.required" => __("validation.required", ["attribute" => __("module_user.name")]),
            "name.min" => __("validation.min.string", ["attribute" => __("module_user.name"), "min" => 2]),
            "name.max" => __("validation.max.string", ["attribute" => __("module_user.name"), "max" => 255]),
            "email.required" => __("validation.required", ["attribute" => __("module_user.email")]),
            "email.email" => __("validation.email", ["attribute" => __("module_user.email")]),
            "email.max" => __("validation.max.string", ["attribute" => __("module_user.email"), "max" => 255]),
            "password.min" => __("validation.min.string", ["attribute" => __("module_user.password"), "min" => 8]),
            "password.max" => __("validation.max.string", ["attribute" => __("module_user.password"), "max" => 255]),
        ]);
    }

    /**
     * Validate before processing.
     *
     * @param array $validatedData
     * @param array $normalizedData
     * @return void
     * @throws \Exception
     */
    protected function validateBeforeProcess(array $validatedData, array $normalizedData): void
    {
        $existingUser = User::query()->where("email", $validatedData["email"])->first();

        if (! $existingUser && empty($validatedData["password"])) {
            throw new \Exception(__("module_user.password_required_for_new_user"));
        }
    }

    /**
     * Process a single row.
     *
     * @param array $validatedData
     * @param array $normalizedData
     * @return void
     */
    protected function processRow(array $validatedData, array $normalizedData): void
    {
        $existingUser = User::query()->where("email", $validatedData["email"])->first();

        if ($existingUser) {
            $user = $existingUser;
            $user->name = $validatedData["name"];

            if (isset($validatedData["password"]) && ! empty($validatedData["password"])) {
                $user->password = $validatedData["password"];
            }
        } else {
            $user = new User();
            $user->name = $validatedData["name"];
            $user->email = $validatedData["email"];
            $user->password = $validatedData["password"];
        }

        $user->save();

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
    }
}
