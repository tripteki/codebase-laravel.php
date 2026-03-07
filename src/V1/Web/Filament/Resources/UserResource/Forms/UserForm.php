<?php

namespace Src\V1\Web\Filament\Resources\UserResource\Forms;

use App\Models\User;
use Src\V1\Api\Acl\Models\Role;
use Src\V1\Api\User\Enums\LogActivityEnum;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rule;

abstract class UserForm
{
    /**
     * @param string $validation
     * @param mixed|null $exception
     * @return array
     */
    public static function validation(string $validation, $exception = null): array
    {
        $tenant = config("tenancy.is_tenancy") ? tenant() : null;
        $tenantId = $exception instanceof User ? $exception->tenant_id : null;

        $nameRule = $tenant
            ? (! $exception ? $tenant->unique("users", "name") : $tenant->unique("users", "name")->ignore($exception))
            : (config("tenancy.is_tenancy")
                ? (filled($tenantId) ? Rule::unique("users", "name")->where("tenant_id", $tenantId) : Rule::unique("users", "name")->whereNull("tenant_id"))
                : Rule::unique("users", "name"));
        if (! $tenant && $exception) {
            $nameRule = $nameRule->ignore($exception);
        }

        $emailRule = $tenant
            ? (! $exception ? $tenant->unique("users", "email") : $tenant->unique("users", "email")->ignore($exception))
            : (config("tenancy.is_tenancy")
                ? (filled($tenantId) ? Rule::unique("users", "email")->where("tenant_id", $tenantId) : Rule::unique("users", "email")->whereNull("tenant_id"))
                : Rule::unique("users", "email"));
        if (! $tenant && $exception) {
            $emailRule = $emailRule->ignore($exception);
        }

        return [

            "name" => [
                "required",
                "string",
                "min:2",
                "max:16",
                $nameRule,
            ],

            "email" => [
                "required",
                "string",
                "min:8",
                "max:48",
                "email",
                $emailRule,
            ],

            "password" => [

                "required",
                "string",
                "min:8",
                "max:16",
            ],

            "password_confirmation" => [

                "string",
                "same:password",
            ],

            "roles" => [

                "nullable",
                "array",
            ],

            "log_activities" => [

                "nullable",
                "array",
            ],

        ][$validation];
    }

    /**
     * @param string $form
     * @return \Filament\Forms\Components\Component
     */
    public static function form(string $form): Component
    {
        return [

            "name" => Forms\Components\TextInput::make("name")->label(__("module.user.labels.name"))->validationAttribute(__("module.user.labels.name"))->
                prefixIcon("heroicon-s-at-symbol")->
                required()->string()->
                minLength(2)->
                maxLength(16)->
                unique(ignoreRecord: true),

            "email" => Forms\Components\TextInput::make("email")->label(__("module.user.labels.email"))->validationAttribute(__("module.user.labels.email"))->
                prefixIcon("heroicon-s-envelope")->
                required()->string()->
                minLength(8)->
                maxLength(48)->
                email()->
                unique(ignoreRecord: true),

            "password" => Forms\Components\TextInput::make("password")->label(__("module.user.labels.password"))->validationAttribute(__("module.user.labels.password"))->
                revealable()->
                dehydrateStateUsing(fn (string $state): string => $state)->
                dehydrated(fn (?string $state): bool => filled($state))->
                required(fn (string $context): bool => $context === "create")->string()->
                password()->
                confirmed()->
                minLength(8)->
                maxLength(16),

            "password_confirmation" => Forms\Components\TextInput::make("password_confirmation")->label(__("module.user.labels.password_confirmation"))->validationAttribute(__("module.user.labels.password_confirmation"))->
                revealable()->
                required(fn (string $context): bool => $context === "create")->string()->
                password(),

            "roles" => Forms\Components\CheckboxList::make("roles")->label(__("module.user.labels.roles"))->validationAttribute(__("module.user.labels.roles"))->
                relationship(titleAttribute: "name")->
                searchable()->
                bulkToggleable()->
                columns(4)->
                gridDirection("row")->
                options(function () {
                    return Role::with("permissions")->get()->mapWithKeys(function ($role) {
                        return [$role->id => $role->name];
                    })->toArray();
                })->
                descriptions(function () {
                    return Role::with("permissions")->get()->mapWithKeys(function ($role) {
                        $permissions = $role->permissions
                            ->filter(fn($p) => str_starts_with($p->name, "user."))
                            ->pluck("name");

                        if ($permissions->isEmpty()) {
                            return [$role->id => __("module.user.messages.no_permissions")];
                        }

                        $html = $permissions->map(fn($p) => e($p))->join('<br />');

                        return [$role->id => new HtmlString($html)];
                    })->toArray();
                }),

            "log_activities" => Forms\Components\CheckboxList::make("log_activities")->label(__("module.user.labels.log_activities"))->validationAttribute(__("module.user.labels.log_activities"))->
                bulkToggleable()->
                columns(4)->
                gridDirection("row")->
                options([
                    LogActivityEnum::CREATED->value => __("module.user.labels.log_activity_created"),
                    LogActivityEnum::UPDATED->value => __("module.user.labels.log_activity_updated"),
                    LogActivityEnum::DELETED->value => __("module.user.labels.log_activity_deleted"),
                    LogActivityEnum::RESTORED->value => __("module.user.labels.log_activity_restored"),
                ])->
                descriptions([
                    LogActivityEnum::CREATED->value => __("module.user.messages.log_activity_created"),
                    LogActivityEnum::UPDATED->value => __("module.user.messages.log_activity_updated"),
                    LogActivityEnum::DELETED->value => __("module.user.messages.log_activity_deleted"),
                    LogActivityEnum::RESTORED->value => __("module.user.messages.log_activity_restored"),
                ]),

        ][$form];
    }
}
